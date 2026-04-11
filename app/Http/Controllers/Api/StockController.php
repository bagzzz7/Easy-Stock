<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StockController extends Controller
{
    /**
     * Record stock in (add stock)
     */
    public function stockIn(Request $request)
    {
        $request->validate([
            'medicine_id' => 'required|exists:medicines,id',
            'quantity' => 'required|integer|min:1',
            'unit_cost' => 'nullable|numeric|min:0',
            'batch_number' => 'nullable|string|max:191',
            'expiry_date' => 'nullable|date',
            'notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        
        try {
            $medicine = Medicine::lockForUpdate()->findOrFail($request->medicine_id);
            $quantityBefore = $medicine->quantity;
            $quantityAfter = $quantityBefore + $request->quantity;

            // Update medicine stock
            $medicine->increment('quantity', $request->quantity);
            
            // Update status based on new quantity
            $medicine->updateStatus();

            // Record stock movement
            StockMovement::create([
                'medicine_id' => $medicine->id,
                'user_id' => Auth::id(),
                'type' => 'stock_in',
                'reason' => 'purchase',
                'quantity' => $request->quantity,
                'quantity_before' => $quantityBefore,
                'quantity_after' => $quantityAfter,
                'batch_number' => $request->batch_number ?? $medicine->batch_number,
                'expiry_date' => $request->expiry_date,
                'unit_cost' => $request->unit_cost ?? $medicine->purchase_price,
                'notes' => $request->notes,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Stock added successfully',
                'data' => [
                    'medicine' => $medicine,
                    'new_quantity' => $medicine->quantity
                ]
            ], 200);
            
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to add stock: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Record stock out (remove stock)
     */
    public function stockOut(Request $request)
    {
        $request->validate([
            'medicine_id' => 'required|exists:medicines,id',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|in:sale,expired,damaged,transfer,adjustment',
            'notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        
        try {
            $medicine = Medicine::lockForUpdate()->findOrFail($request->medicine_id);
            
            // Check if enough stock is available
            if ($medicine->quantity < $request->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => "Insufficient stock. Available: {$medicine->quantity}"
                ], 400);
            }

            $quantityBefore = $medicine->quantity;
            $quantityAfter = $quantityBefore - $request->quantity;

            // Update medicine stock
            $medicine->decrement('quantity', $request->quantity);
            
            // Update status based on new quantity
            $medicine->updateStatus();

            // Record stock movement
            StockMovement::create([
                'medicine_id' => $medicine->id,
                'user_id' => Auth::id(),
                'type' => 'stock_out',
                'reason' => $request->reason,
                'quantity' => $request->quantity,
                'quantity_before' => $quantityBefore,
                'quantity_after' => $quantityAfter,
                'batch_number' => $medicine->batch_number,
                'notes' => $request->notes,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Stock removed successfully',
                'data' => [
                    'medicine' => $medicine,
                    'new_quantity' => $medicine->quantity
                ]
            ], 200);
            
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove stock: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get stock movement history
     */
    public function history(Request $request)
    {
        $query = StockMovement::with(['medicine', 'user'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('medicine_id')) {
            $query->where('medicine_id', $request->medicine_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $movements = $query->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $movements
        ]);
    }

    /**
     * Get stock movements summary
     */
    public function movements(Request $request)
    {
        $todayIn = StockMovement::where('type', 'stock_in')
            ->whereDate('created_at', today())
            ->sum('quantity');

        $todayOut = StockMovement::where('type', 'stock_out')
            ->whereDate('created_at', today())
            ->sum('quantity');

        $totalIn = StockMovement::where('type', 'stock_in')
            ->sum('quantity');

        $totalOut = StockMovement::where('type', 'stock_out')
            ->sum('quantity');

        $recentMovements = StockMovement::with(['medicine', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'today_in' => $todayIn,
                'today_out' => $todayOut,
                'total_in' => $totalIn,
                'total_out' => $totalOut,
                'recent_movements' => $recentMovements
            ]
        ]);
    }
}
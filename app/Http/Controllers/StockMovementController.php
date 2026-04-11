<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockMovementController extends Controller
{
    // ── Transaction History ──────────────────────────────────────

    public function index(Request $request)
    {
        $query = StockMovement::with(['medicine', 'user', 'supplier'])
            ->latest();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('medicine_id')) {
            $query->where('medicine_id', $request->medicine_id);
        }

        if ($request->filled('reason')) {
            $query->where('reason', $request->reason);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $movements = $query->paginate(20)->withQueryString();
        $medicines = Medicine::orderBy('name')->get(['id', 'name']);

        // Summary stats for header
        $todayIn  = StockMovement::where('type', 'stock_in')
            ->whereDate('created_at', today())->sum('quantity');
        $todayOut = StockMovement::where('type', 'stock_out')
            ->whereDate('created_at', today())->sum('quantity');
        $totalMovements = StockMovement::count();

        return view('stock.history', compact(
            'movements', 'medicines', 'todayIn', 'todayOut', 'totalMovements'
        ));
    }

    // ── Stock In Form ────────────────────────────────────────────

    public function stockInForm()
    {
        $medicines = Medicine::orderBy('name')->get(['id', 'name', 'brand', 'unit', 'quantity', 'purchase_price', 'batch_number']);
        $suppliers = Supplier::orderBy('name')->get(['id', 'name']);

        return view('stock.stock-in', compact('medicines', 'suppliers'));
    }

    // ── Stock In Store ───────────────────────────────────────────

    public function stockIn(Request $request)
    {
        $validated = $request->validate([
            'medicine_id'  => 'required|exists:medicines,id',
            'supplier_id'  => 'nullable|exists:suppliers,id',
            'quantity'     => 'required|integer|min:1',
            'batch_number' => 'nullable|string|max:191',
            'expiry_date'  => 'nullable|date|after:today',
            'unit_cost'    => 'nullable|numeric|min:0',
            'notes'        => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($validated) {
            $medicine = Medicine::lockForUpdate()->findOrFail($validated['medicine_id']);

            $quantityBefore = $medicine->quantity;
            $quantityAfter  = $quantityBefore + $validated['quantity'];

            // Update medicine stock
            $medicine->increment('quantity', $validated['quantity']);
            $medicine->updateStatus();

            // Record movement
            StockMovement::create([
                'medicine_id'     => $medicine->id,
                'user_id'         => auth()->id(),
                'supplier_id'     => $validated['supplier_id'] ?? null,
                'type'            => 'stock_in',
                'reason'          => 'purchase',
                'quantity'        => $validated['quantity'],
                'quantity_before' => $quantityBefore,
                'quantity_after'  => $quantityAfter,
                'batch_number'    => $validated['batch_number'] ?? $medicine->batch_number,
                'expiry_date'     => $validated['expiry_date'] ?? null,
                'unit_cost'       => $validated['unit_cost'] ?? $medicine->purchase_price,
                'notes'           => $validated['notes'] ?? null,
            ]);
        });

        return redirect()->route('stock.history')
            ->with('success', 'Stock added successfully.');
    }

    // ── Stock Out Form ───────────────────────────────────────────

    public function stockOutForm()
    {
        $medicines = Medicine::where('quantity', '>', 0)
            ->orderBy('name')
            ->get(['id', 'name', 'brand', 'unit', 'quantity', 'status']);

        return view('stock.stock-out', compact('medicines'));
    }

    // ── Stock Out Store ──────────────────────────────────────────

    public function stockOut(Request $request)
    {
        $validated = $request->validate([
            'medicine_id' => 'required|exists:medicines,id',
            'quantity'    => 'required|integer|min:1',
            'reason'      => 'required|in:sale,expired,damaged,transfer,adjustment',
            'notes'       => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($validated) {
            $medicine = Medicine::lockForUpdate()->findOrFail($validated['medicine_id']);

            if ($medicine->quantity < $validated['quantity']) {
                throw new \Exception("Insufficient stock. Available: {$medicine->quantity}");
            }

            $quantityBefore = $medicine->quantity;
            $quantityAfter  = $quantityBefore - $validated['quantity'];

            // Deduct stock
            $medicine->decrement('quantity', $validated['quantity']);
            $medicine->updateStatus();

            // Record movement
            StockMovement::create([
                'medicine_id'     => $medicine->id,
                'user_id'         => auth()->id(),
                'supplier_id'     => null,
                'type'            => 'stock_out',
                'reason'          => $validated['reason'],
                'quantity'        => $validated['quantity'],
                'quantity_before' => $quantityBefore,
                'quantity_after'  => $quantityAfter,
                'batch_number'    => $medicine->batch_number,
                'expiry_date'     => null,
                'unit_cost'       => null,
                'notes'           => $validated['notes'] ?? null,
            ]);
        });

        return redirect()->route('stock.history')
            ->with('success', 'Stock deducted successfully.');
    }
}
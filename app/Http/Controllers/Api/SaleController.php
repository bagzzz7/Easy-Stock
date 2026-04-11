<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Medicine;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SaleController extends Controller
{
    /**
     * List sales.
     * GET /api/sales?period=today|week|month
     */
    public function index(Request $request): JsonResponse
    {
        $query = Sale::with(['items.medicine', 'user'])->latest();

        if ($request->filled('period')) {
            match ($request->period) {
                'today' => $query->whereDate('created_at', today()),
                'week'  => $query->where('created_at', '>=', now()->startOfWeek()),
                'month' => $query->where('created_at', '>=', now()->startOfMonth()),
                default => null,
            };
        }

        return response()->json($query->paginate(20));
    }

    /**
     * Show a single sale.
     * GET /api/sales/{id}
     */
    public function show(Sale $sale): JsonResponse
    {
        return response()->json($sale->load(['items.medicine', 'user']));
    }

    /**
     * Create a new sale.
     * POST /api/sales
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'items'                  => 'required|array|min:1',
            'items.*.medicine_id'    => 'required|exists:medicines,id',
            'items.*.quantity'       => 'required|integer|min:1',
            'items.*.unit_price'     => 'required|numeric|min:0',
            'payment_method'         => 'required|in:cash,card,mobile_payment',
            'discount'               => 'nullable|numeric|min:0',
            'tax'                    => 'nullable|numeric|min:0',
            'total_amount'           => 'required|numeric|min:0',
            'grand_total'            => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Create the sale
            $sale = Sale::create([
                'user_id'        => $request->user()->id,
                'invoice_number' => 'INV-' . now()->format('Ymd') . '-' . strtoupper(Str::random(8)),
                'payment_method' => $request->payment_method,
                'discount'       => $request->discount ?? 0,
                'tax'            => $request->tax ?? 0,
                'total_amount'   => $request->total_amount,
                'grand_total'    => $request->grand_total,
            ]);

            // Create sale items and decrement stock
            foreach ($request->items as $item) {
                $medicine = Medicine::findOrFail($item['medicine_id']);

                // Check stock
                if ($medicine->quantity < $item['quantity']) {
                    DB::rollBack();
                    return response()->json([
                        'message' => "Insufficient stock for {$medicine->name}. Only {$medicine->quantity} units available."
                    ], 422);
                }

                $subtotal = $item['quantity'] * $item['unit_price'];

                $sale->items()->create([
                    'medicine_id' => $item['medicine_id'],
                    'quantity'    => $item['quantity'],
                    'unit_price'  => $item['unit_price'],
                    'subtotal'    => $subtotal,
                ]);

                // Decrement stock and update status
                $medicine->decrement('quantity', $item['quantity']);
                $medicine->updateStatus();
            }

            DB::commit();

            return response()->json($sale->load(['items.medicine', 'user']), 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to process sale: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get invoice for a sale.
     * GET /api/sales/{sale}/invoice
     */
    public function invoice(Sale $sale): JsonResponse
    {
        $sale->load(['items.medicine', 'user']);

        return response()->json([
            'invoice_number' => $sale->invoice_number,
            'date'           => $sale->created_at->format('M d, Y h:i A'),
            'cashier'        => $sale->user?->name,
            'items'          => $sale->items->map(fn ($item) => [
                'name'      => $item->medicine->name,
                'quantity'  => $item->quantity,
                'unit_price'=> $item->unit_price,
                'subtotal'  => $item->subtotal,
            ]),
            'subtotal'       => $sale->total_amount,
            'discount'       => $sale->discount,
            'tax'            => $sale->tax,
            'grand_total'    => $sale->grand_total,
            'payment_method' => $sale->payment_method,
        ]);
    }
}
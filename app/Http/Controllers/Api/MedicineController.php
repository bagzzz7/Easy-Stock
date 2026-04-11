<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class MedicineController extends Controller
{
    /**
     * List all medicines with optional filters.
     * GET /api/medicines?search=para&status=in_stock&category_id=1
     */
    public function index(Request $request): JsonResponse
    {
        $query = Medicine::with(['category', 'supplier']);

        // Search
        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($q2) use ($q) {
                $q2->where('name',         'LIKE', "%{$q}%")
                   ->orWhere('generic_name','LIKE', "%{$q}%")
                   ->orWhere('brand',       'LIKE', "%{$q}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $medicines = $query->orderBy('name')->paginate(20);

        return response()->json($medicines);
    }

    /**
     * Show a single medicine.
     * GET /api/medicines/{id}
     */
    public function show(Medicine $medicine): JsonResponse
    {
        return response()->json($medicine->load(['category', 'supplier']));
    }

    /**
     * Create a new medicine.
     * POST /api/medicines
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name'                  => 'required|string|max:255',
            'generic_name'          => 'required|string|max:255',
            'brand'                 => 'required|string|max:255',
            'category_id'           => 'nullable|exists:categories,id',
            'supplier_id'           => 'nullable|exists:suppliers,id',
            'purchase_price'        => 'required|numeric|min:0',
            'selling_price'         => 'required|numeric|min:0',
            'quantity'              => 'required|integer|min:0',
            'reorder_level'         => 'required|integer|min:0',
            'expiry_date'           => 'required|date',
            'requires_prescription' => 'boolean',
        ]);

        $medicine = Medicine::create($request->all());
        $medicine->updateStatus();

        return response()->json($medicine->load(['category', 'supplier']), 201);
    }

    /**
     * Update a medicine.
     * PUT /api/medicines/{id}
     */
    public function update(Request $request, Medicine $medicine): JsonResponse
    {
        $request->validate([
            'name'           => 'sometimes|string|max:255',
            'selling_price'  => 'sometimes|numeric|min:0',
            'purchase_price' => 'sometimes|numeric|min:0',
            'quantity'       => 'sometimes|integer|min:0',
            'reorder_level'  => 'sometimes|integer|min:0',
            'expiry_date'    => 'sometimes|date',
        ]);

        $medicine->update($request->all());
        $medicine->updateStatus();

        return response()->json($medicine->load(['category', 'supplier']));
    }

    /**
     * Delete a medicine.
     * DELETE /api/medicines/{id}
     */
    public function destroy(Medicine $medicine): JsonResponse
    {
        $medicine->delete();

        return response()->json(['message' => 'Medicine deleted successfully.']);
    }

    /**
     * Search medicines (dedicated search endpoint).
     * GET /api/medicines/search?q=paracetamol
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate(['q' => 'required|string|min:2']);

        $medicines = Medicine::with(['category'])
            ->where(function ($query) use ($request) {
                $q = $request->q;
                $query->where('name',          'LIKE', "%{$q}%")
                      ->orWhere('generic_name', 'LIKE', "%{$q}%")
                      ->orWhere('brand',        'LIKE', "%{$q}%");
            })
            ->where('status', '!=', 'expired')
            ->orderBy('name')
            ->limit(15)
            ->get();

        return response()->json($medicines);
    }

    /**
     * Get low stock medicines.
     * GET /api/medicines/low-stock
     */
    public function lowStock(): JsonResponse
    {
        $medicines = Medicine::with(['category'])
            ->where('quantity', '<=', DB::raw('reorder_level'))
            ->where('status', '!=', 'expired')
            ->orderBy('quantity', 'asc')
            ->get();

        return response()->json($medicines);
    }

    /**
     * Get expiring medicines (within 60 days).
     * GET /api/medicines/expiring
     */
    public function expiring(): JsonResponse
    {
        $medicines = Medicine::with(['category'])
            ->where('expiry_date', '>', now())
            ->where('expiry_date', '<=', now()->addDays(60))
            ->where('status', '!=', 'expired')
            ->orderBy('expiry_date', 'asc')
            ->get();

        return response()->json($medicines);
    }
}
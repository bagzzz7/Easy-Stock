<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    /**
     * Display a listing of suppliers.
     * GET /api/suppliers
     */
    public function index(Request $request): JsonResponse
    {
        $query = Supplier::query();

        // Search by name, email, phone, or contact person
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%")
                  ->orWhere('contact_person', 'LIKE', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $suppliers = $query->withCount('medicines')
            ->orderBy('name')
            ->paginate(20);

        return response()->json($suppliers);
    }

    /**
     * Store a newly created supplier.
     * POST /api/suppliers
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:suppliers,email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'contact_person' => 'nullable|string|max:255',
        ]);

        $supplier = Supplier::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Supplier created successfully',
            'data' => $supplier
        ], 201);
    }

    /**
     * Display the specified supplier.
     * GET /api/suppliers/{id}
     */
    public function show(Supplier $supplier): JsonResponse
    {
        $supplier->load(['medicines' => function ($query) {
            $query->select('id', 'name', 'brand', 'selling_price', 'quantity', 'expiry_date', 'status')
                  ->orderBy('name');
        }]);

        return response()->json([
            'success' => true,
            'data' => $supplier
        ]);
    }

    /**
     * Update the specified supplier.
     * PUT /api/suppliers/{id}
     */
    public function update(Request $request, Supplier $supplier): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:suppliers,email,' . $supplier->id,
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'contact_person' => 'nullable|string|max:255',
        ]);

        $supplier->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Supplier updated successfully',
            'data' => $supplier
        ]);
    }

    /**
     * Remove the specified supplier.
     * DELETE /api/suppliers/{id}
     */
    public function destroy(Supplier $supplier): JsonResponse
    {
        // Check if supplier has any medicines
        if ($supplier->medicines()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete supplier with existing products'
            ], 400);
        }

        $supplier->delete();

        return response()->json([
            'success' => true,
            'message' => 'Supplier deleted successfully'
        ]);
    }
}
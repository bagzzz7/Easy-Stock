<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MedicineController extends Controller
{
    public function index(Request $request)
    {
        $query = Medicine::with(['category', 'supplier']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name',           'like', "%{$search}%")
                  ->orWhere('generic_name', 'like', "%{$search}%")
                  ->orWhere('brand',        'like', "%{$search}%")
                  ->orWhere('batch_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('category_type')) {
            $query->where('category_type', $request->category_type);
        }

        $medicines  = $query->orderBy('name')->paginate(20)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('medicines.index', compact('medicines', 'categories'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $suppliers  = Supplier::orderBy('name')->get();
        return view('medicines.create', compact('categories', 'suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                  => 'required|string|max:255',
            'generic_name'          => 'required|string|max:255',
            'brand'                 => 'required|string|max:255',
            'description'           => 'nullable|string',
            'category_id'           => 'required|exists:categories,id',
            'supplier_id'           => 'required|exists:suppliers,id',
            'purchase_price'        => 'required|numeric|min:0',
            'selling_price'         => 'required|numeric|min:0|gte:purchase_price',
            'quantity'              => 'required|integer|min:0',
            'reorder_level'         => 'required|integer|min:1',
            'expiry_date'           => 'required|date|after:today',
            'batch_number'          => 'required|string|max:255|unique:medicines',
            'shelf_number'          => 'nullable|string|max:50',
            'requires_prescription' => 'boolean',
        ]);

        $validated['requires_prescription'] = $request->boolean('requires_prescription');

        Medicine::create($validated);

        return redirect()->route('medicines.index')
            ->with('success', 'Medicine added successfully.');
    }

    public function show(Medicine $medicine)
    {
        $medicine->load(['category', 'supplier', 'saleItems' => function ($q) {
            $q->with('sale.user')->latest()->limit(10);
        }]);

        return view('medicines.show', compact('medicine'));
    }

    public function edit(Medicine $medicine)
    {
        $categories = Category::orderBy('name')->get();
        $suppliers  = Supplier::orderBy('name')->get();
        return view('medicines.create', compact('medicine', 'categories', 'suppliers'));
    }

    public function update(Request $request, Medicine $medicine)
    {
        $validated = $request->validate([
            'name'                  => 'required|string|max:255',
            'generic_name'          => 'required|string|max:255',
            'brand'                 => 'required|string|max:255',
            'description'           => 'nullable|string',
            'category_id'           => 'required|exists:categories,id',
            'supplier_id'           => 'required|exists:suppliers,id',
            'purchase_price'        => 'required|numeric|min:0',
            'selling_price'         => 'required|numeric|min:0|gte:purchase_price',
            'quantity'              => 'required|integer|min:0',
            'reorder_level'         => 'required|integer|min:1',
            'expiry_date'           => 'required|date',
            'batch_number'          => 'required|string|max:255|unique:medicines,batch_number,' . $medicine->id,
            'shelf_number'          => 'nullable|string|max:50',
            'requires_prescription' => 'boolean',
        ]);

        $validated['requires_prescription'] = $request->boolean('requires_prescription');

        $medicine->update($validated);

        return redirect()->route('medicines.index')
            ->with('success', 'Medicine updated successfully.');
    }

    public function destroy(Medicine $medicine)
    {
        if ($medicine->saleItems()->exists()) {
            return redirect()->back()
                ->with('error', 'Cannot delete medicine with sales history.');
        }

        $medicine->delete();

        return redirect()->route('medicines.index')
            ->with('success', 'Medicine deleted successfully.');
    }

    public function restock(Request $request, Medicine $medicine)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);

        $medicine->increment('quantity', $request->quantity);
        $medicine->updateStatus();

        return redirect()->back()
            ->with('success', "Added {$request->quantity} units to {$medicine->name}.");
    }

    public function search(Request $request)
    {
        $query = $request->get('q');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $medicines = Medicine::where(function ($q) use ($query) {
                $q->where('name',          'like', "%{$query}%")
                  ->orWhere('generic_name', 'like', "%{$query}%")
                  ->orWhere('brand',        'like', "%{$query}%");
            })
            ->where('status', '!=', 'expired')
            ->where('quantity', '>', 0)
            ->limit(10)
            ->get(['id', 'name', 'brand', 'selling_price', 'quantity']);

        return response()->json($medicines);
    }

    public function lowStock()
    {
        $medicines = Medicine::where('status', 'low_stock')
            ->with(['category', 'supplier'])
            ->orderBy('quantity')
            ->paginate(15);

        return view('medicines.low-stock', compact('medicines'));
    }

    public function expiring()
    {
        $medicines = Medicine::where('expiry_date', '>', now())
            ->where('expiry_date', '<=', now()->addDays(30))
            ->where('status', '!=', 'expired')
            ->with(['category', 'supplier'])
            ->orderBy('expiry_date')
            ->paginate(15);

        return view('medicines.expiring', compact('medicines'));
    }

    public function export()
    {
        // Placeholder — implement export logic here
        return redirect()->back()->with('info', 'Export feature coming soon.');
    }
}
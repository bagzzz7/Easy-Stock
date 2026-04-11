<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    
    public function index(Request $request)
    {
        $query = Supplier::withCount('medicines');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('license_number', 'like', "%{$search}%");
            });
        }

        $suppliers = $query->latest()->paginate(15);

        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:suppliers,email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'contact_person' => 'nullable|string|max:255',
            'license_number' => 'nullable|string|max:100|unique:suppliers,license_number',
        ]);

        Supplier::create($validated);

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier added successfully.');
    }

    public function show(Supplier $supplier)
    {
        $supplier->load(['medicines' => function($q) {
            $q->latest()->limit(10);
        }]);

        return view('suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        // Optional: Restrict edit to admin only
        if (!auth()->user()->isAdministrator()) {
            abort(403, 'Only administrators can edit suppliers.');
        }
        
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        // Optional: Restrict update to admin only
        if (!auth()->user()->isAdministrator()) {
            abort(403, 'Only administrators can update suppliers.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:suppliers,email,' . $supplier->id,
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'contact_person' => 'nullable|string|max:255',
            'license_number' => 'nullable|string|max:100|unique:suppliers,license_number,' . $supplier->id,
        ]);

        $supplier->update($validated);

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        // Optional: Restrict delete to admin only
        if (!auth()->user()->isAdministrator()) {
            abort(403, 'Only administrators can delete suppliers.');
        }
        
        if ($supplier->medicines()->exists()) {
            return redirect()->back()
                ->with('error', 'Cannot delete supplier with associated medicines.');
        }

        $supplier->delete();

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier deleted successfully.');
    }

    public function toggleStatus(Supplier $supplier)
    {
        // Optional: Restrict toggle status to admin only
        if (!auth()->user()->isAdministrator()) {
            abort(403, 'Only administrators can change supplier status.');
        }
        
        $supplier->update(['is_active' => !$supplier->is_active]);

        $status = $supplier->is_active ? 'activated' : 'deactivated';

        return redirect()->back()
            ->with('success', "Supplier {$status} successfully.");
    }
}
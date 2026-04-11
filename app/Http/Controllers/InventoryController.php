<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        // ── Paginated medicine list (overview tab) ────────────────────
        $query = Medicine::with(['category', 'supplier']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name',          'like', "%{$s}%")
                  ->orWhere('generic_name', 'like', "%{$s}%")
                  ->orWhere('brand',        'like', "%{$s}%")
                  ->orWhere('batch_number', 'like', "%{$s}%");
            });
        }
        if ($request->filled('category_id')) $query->where('category_id', $request->category_id);
        if ($request->filled('status'))      $query->where('status', $request->status);

        match ($request->get('sort', 'name')) {
            'quantity' => $query->orderBy('quantity', 'asc'),
            'expiry'   => $query->orderBy('expiry_date', 'asc'),
            'value'    => $query->orderByRaw('quantity * purchase_price DESC'),
            default    => $query->orderBy('name', 'asc'),
        };

        $medicines = $query->paginate(20)->withQueryString();

        // ── Full medicine list for Stock In/Out dropdowns ─────────────
        $medicines_all = Medicine::orderBy('name')
            ->get(['id', 'name', 'brand', 'unit', 'quantity', 'purchase_price', 'batch_number', 'status']);

        // ── KPI counts ────────────────────────────────────────────────
        $totalMedicines  = Medicine::count();
        $inStockCount    = Medicine::where('status', 'in_stock')->count();
        $lowStockCount   = Medicine::where('status', 'low_stock')->count();
        $outOfStockCount = Medicine::where('status', 'out_of_stock')->count();
        $expiredCount    = Medicine::where('status', 'expired')->count();
        $totalStockValue = Medicine::selectRaw('SUM(quantity * purchase_price) as total')->value('total') ?? 0;

        // ── Transaction history (history tab) ─────────────────────────
        $histQuery = StockMovement::with(['medicine', 'user', 'supplier'])->latest();

        if ($request->filled('hist_type'))     $histQuery->where('type',        $request->hist_type);
        if ($request->filled('hist_medicine')) $histQuery->where('medicine_id', $request->hist_medicine);
        if ($request->filled('hist_reason'))   $histQuery->where('reason',      $request->hist_reason);
        if ($request->filled('hist_from'))     $histQuery->whereDate('created_at', '>=', $request->hist_from);
        if ($request->filled('hist_to'))       $histQuery->whereDate('created_at', '<=', $request->hist_to);

        $movements = $histQuery->paginate(20)->withQueryString();

        // ── History summary stats ─────────────────────────────────────
        $totalMovements = StockMovement::count();
        $todayIn        = StockMovement::where('type', 'stock_in')->whereDate('created_at', today())->sum('quantity');
        $todayOut       = StockMovement::where('type', 'stock_out')->whereDate('created_at', today())->sum('quantity');

        // ── Recent sidebars ───────────────────────────────────────────
        $recentStockIn  = StockMovement::with(['medicine', 'user'])->where('type', 'stock_in')->latest()->limit(8)->get();
        $recentStockOut = StockMovement::with(['medicine', 'user'])->where('type', 'stock_out')->latest()->limit(8)->get();

        // ── Filter data ───────────────────────────────────────────────
        $categories = Category::orderBy('name')->get();
        $suppliers  = Supplier::orderBy('name')->get(['id', 'name']);

        return view('inventory', compact(
            'medicines', 'medicines_all',
            'categories', 'suppliers',
            'totalMedicines', 'inStockCount', 'lowStockCount',
            'outOfStockCount', 'expiredCount', 'totalStockValue',
            'movements', 'totalMovements', 'todayIn', 'todayOut',
            'recentStockIn', 'recentStockOut'
        ));
    }
}
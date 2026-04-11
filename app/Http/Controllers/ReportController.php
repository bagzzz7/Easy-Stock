<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Medicine;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
   

    /**
     * Sales Report
     */
    public function sales(Request $request)
    {
        $query = Sale::with('user', 'items.medicine');

        // Date filters
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Payment method filter
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        $sales = $query->latest()->paginate(15)->withQueryString();

        // Calculate totals
        $totalSales = $sales->total();
        $totalRevenue = $sales->sum('grand_total');
        $totalItems = $sales->sum(function($sale) {
            return $sale->items->sum('quantity');
        });

        // Get sales by payment method
        $paymentMethodStats = Sale::when($request->filled('date_from'), function($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->date_from);
            })
            ->when($request->filled('date_to'), function($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->date_to);
            })
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(grand_total) as total'))
            ->groupBy('payment_method')
            ->get();

        return view('reports.sales', compact('sales', 'totalSales', 'totalRevenue', 'totalItems', 'paymentMethodStats'));
    }

    /**
     * Stock Alert Report
     */
    /**
 * Stock Alert Report
 */
public function stockAlert(Request $request)
{
    $query = Medicine::with('category', 'supplier')
        ->where('quantity', '<=', DB::raw('reorder_level'));

    // Category filter
    if ($request->filled('category_id')) {
        $query->where('category_id', $request->category_id);
    }

    // Supplier filter
    if ($request->filled('supplier_id')) {
        $query->where('supplier_id', $request->supplier_id);
    }

    // Stock status filter
    if ($request->filled('stock_status')) {
        if ($request->stock_status === 'critical') {
            $query->where('quantity', '<=', 5);
        } elseif ($request->stock_status === 'low') {
            $query->whereBetween('quantity', [6, DB::raw('reorder_level')]);
        }
    }

    $medicines = $query->orderBy('quantity', 'asc')->paginate(15)->withQueryString();

    // Get categories and suppliers for filters - REMOVED is_active filter
    $categories = Category::all();
    $suppliers = Supplier::all(); // Changed this line

    // Calculate statistics
    $totalLowStock = Medicine::where('quantity', '<=', DB::raw('reorder_level'))->count();
    $criticalStock = Medicine::where('quantity', '<=', 5)->count();
    $outOfStock = Medicine::where('quantity', 0)->count();

    return view('reports.stock-alert', compact('medicines', 'categories', 'suppliers', 'totalLowStock', 'criticalStock', 'outOfStock'));
}

    /**
     * Expiry Report
     */
    public function expiry(Request $request)
    {
        $query = Medicine::with('category', 'supplier')
            ->where('expiry_date', '>=', now())
            ->where('expiry_date', '<=', now()->addMonths(6)); // Show medicines expiring in next 6 months

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('expiry_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('expiry_date', '<=', $request->date_to);
        }

        // Category filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Expiry period filter
        if ($request->filled('expiry_period')) {
            switch ($request->expiry_period) {
                case '1month':
                    $query->where('expiry_date', '<=', now()->addMonth());
                    break;
                case '3months':
                    $query->where('expiry_date', '<=', now()->addMonths(3));
                    break;
                case '6months':
                    $query->where('expiry_date', '<=', now()->addMonths(6));
                    break;
            }
        }

        $medicines = $query->orderBy('expiry_date', 'asc')->paginate(15)->withQueryString();

        // Get categories for filter
        $categories = Category::all();

        // Calculate statistics
        $expiringNextMonth = Medicine::where('expiry_date', '<=', now()->addMonth())->count();
        $expiringNext3Months = Medicine::where('expiry_date', '<=', now()->addMonths(3))->count();
        $expiringNext6Months = Medicine::where('expiry_date', '<=', now()->addMonths(6))->count();
        $expired = Medicine::where('expiry_date', '<', now())->count();

        return view('reports.expiry', compact('medicines', 'categories', 'expiringNextMonth', 'expiringNext3Months', 'expiringNext6Months', 'expired'));
    }
}
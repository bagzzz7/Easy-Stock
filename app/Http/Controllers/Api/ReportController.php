<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Sales report.
     * GET /api/reports/sales?period=today|week|month|year&date_from=&date_to=&payment_method=
     */
    public function sales(Request $request): JsonResponse
    {
        $period = $request->get('period', 'month');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $paymentMethod = $request->get('payment_method');

        $query = Sale::with(['user', 'items.medicine']);

        // Apply filters
        if ($dateFrom && $dateTo) {
            $query->whereBetween('created_at', [$dateFrom, $dateTo]);
        } elseif ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        } elseif ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        } else {
            // Default period filter
            match ($period) {
                'today' => $query->whereDate('created_at', today()),
                'week'  => $query->where('created_at', '>=', now()->startOfWeek()),
                'year'  => $query->whereYear('created_at', now()->year),
                default => $query->whereMonth('created_at', now()->month)
                                 ->whereYear('created_at', now()->year),
            };
        }

        // Filter by payment method
        if ($paymentMethod) {
            $query->where('payment_method', $paymentMethod);
        }

        // Get paginated sales for the list
        $sales = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get all sales for summary (without pagination)
        $allSalesQuery = clone $query;
        $allSales = $allSalesQuery->get();

        $totalTransactions = $allSales->count();
        $totalRevenue = $allSales->sum('grand_total');
        $averageSale = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;
        
        $totalItemsSold = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->when($dateFrom && $dateTo, fn($q) => $q->whereBetween('sales.created_at', [$dateFrom, $dateTo]))
            ->when($dateFrom, fn($q) => $q->whereDate('sales.created_at', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('sales.created_at', '<=', $dateTo))
            ->when($paymentMethod, fn($q) => $q->where('sales.payment_method', $paymentMethod))
            ->when(!$dateFrom && !$dateTo, function($q) use ($period) {
                match ($period) {
                    'today' => $q->whereDate('sales.created_at', today()),
                    'week'  => $q->where('sales.created_at', '>=', now()->startOfWeek()),
                    'year'  => $q->whereYear('sales.created_at', now()->year),
                    default => $q->whereMonth('sales.created_at', now()->month)
                                 ->whereYear('sales.created_at', now()->year),
                };
            })
            ->sum('sale_items.quantity');

        // Top selling medicines
        $topMedicines = DB::table('sale_items')
            ->join('medicines', 'sale_items.medicine_id', '=', 'medicines.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->when($dateFrom && $dateTo, fn($q) => $q->whereBetween('sales.created_at', [$dateFrom, $dateTo]))
            ->when($dateFrom, fn($q) => $q->whereDate('sales.created_at', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('sales.created_at', '<=', $dateTo))
            ->when($paymentMethod, fn($q) => $q->where('sales.payment_method', $paymentMethod))
            ->when(!$dateFrom && !$dateTo, function($q) use ($period) {
                match ($period) {
                    'today' => $q->whereDate('sales.created_at', today()),
                    'week'  => $q->where('sales.created_at', '>=', now()->startOfWeek()),
                    'year'  => $q->whereYear('sales.created_at', now()->year),
                    default => $q->whereMonth('sales.created_at', now()->month)
                                 ->whereYear('sales.created_at', now()->year),
                };
            })
            ->select(
                'medicines.id',
                'medicines.name',
                'medicines.generic_name',
                DB::raw('SUM(sale_items.quantity) as total_quantity'),
                DB::raw('SUM(sale_items.subtotal) as total_revenue')
            )
            ->groupBy('medicines.id', 'medicines.name', 'medicines.generic_name')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();

        // Payment breakdown
        $paymentBreakdown = $allSales->groupBy('payment_method')
            ->map(fn($group) => [
                'count' => $group->count(),
                'total' => $group->sum('grand_total')
            ]);

        return response()->json([
            'summary' => [
                'period'             => $period,
                'total_transactions' => $totalTransactions,
                'total_revenue'      => round($totalRevenue, 2),
                'average_sale'       => round($averageSale, 2),
                'total_items_sold'   => $totalItemsSold,
            ],
            'top_medicines'      => $topMedicines,
            'payment_breakdown'  => $paymentBreakdown,
            'sales'              => $sales, // This now includes the paginated sales list
        ]);
    }

    /**
     * Stock alert report.
     * GET /api/reports/stock-alert?category_id=&supplier_id=&stock_status=
     */
    public function stockAlert(Request $request): JsonResponse
    {
        $categoryId = $request->get('category_id');
        $supplierId = $request->get('supplier_id');
        $stockStatus = $request->get('stock_status');

        $query = Medicine::with(['category', 'supplier'])
            ->where('quantity', '<=', DB::raw('reorder_level'))
            ->where('status', '!=', 'expired');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }
        if ($supplierId) {
            $query->where('supplier_id', $supplierId);
        }
        if ($stockStatus === 'critical') {
            $query->where('quantity', '<=', 5);
        } elseif ($stockStatus === 'low') {
            $query->whereBetween('quantity', [6, 10]);
        }

        $medicines = $query->orderBy('quantity', 'asc')->paginate(20);
        
        $totalLowStock = Medicine::where('quantity', '<=', DB::raw('reorder_level'))
            ->where('status', '!=', 'expired')
            ->count();
        
        $criticalStock = Medicine::where('quantity', '<=', 5)
            ->where('status', '!=', 'expired')
            ->count();
        
        $outOfStock = Medicine::where('status', 'out_of_stock')->count();

        return response()->json([
            'summary' => [
                'total_low_stock' => $totalLowStock,
                'critical_stock' => $criticalStock,
                'out_of_stock' => $outOfStock,
            ],
            'medicines' => $medicines,
        ]);
    }

    /**
     * Expiry report.
     * GET /api/reports/expiry?expiry_period=&category_id=&date_from=&date_to=
     */
    public function expiry(Request $request): JsonResponse
    {
        $expiryPeriod = $request->get('expiry_period');
        $categoryId = $request->get('category_id');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        $query = Medicine::with(['category']);

        // Apply date range filters
        if ($dateFrom && $dateTo) {
            $query->whereBetween('expiry_date', [$dateFrom, $dateTo]);
        } elseif ($dateFrom) {
            $query->whereDate('expiry_date', '>=', $dateFrom);
        } elseif ($dateTo) {
            $query->whereDate('expiry_date', '<=', $dateTo);
        } elseif ($expiryPeriod) {
            switch ($expiryPeriod) {
                case '1month':
                    $query->where('expiry_date', '>', now())
                          ->where('expiry_date', '<=', now()->addDays(30));
                    break;
                case '3months':
                    $query->where('expiry_date', '>', now())
                          ->where('expiry_date', '<=', now()->addMonths(3));
                    break;
                case '6months':
                    $query->where('expiry_date', '>', now())
                          ->where('expiry_date', '<=', now()->addMonths(6));
                    break;
                default:
                    $query->where('expiry_date', '>', now())
                          ->where('expiry_date', '<=', now()->addMonths(6));
            }
        } else {
            $query->where('expiry_date', '>', now())
                  ->where('expiry_date', '<=', now()->addMonths(6));
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $medicines = $query->orderBy('expiry_date', 'asc')->paginate(20);
        
        // Add days_left to each medicine
        foreach ($medicines as $medicine) {
            $medicine->days_left = now()->diffInDays($medicine->expiry_date, false);
        }

        // Summary counts
        $expired = Medicine::where('status', 'expired')->count();
        $expiringNextMonth = Medicine::where('expiry_date', '>', now())
            ->where('expiry_date', '<=', now()->addDays(30))
            ->count();
        $expiringNext3Months = Medicine::where('expiry_date', '>', now())
            ->where('expiry_date', '<=', now()->addMonths(3))
            ->count();
        $expiringNext6Months = Medicine::where('expiry_date', '>', now())
            ->where('expiry_date', '<=', now()->addMonths(6))
            ->count();

        return response()->json([
            'summary' => [
                'expired' => $expired,
                'expiring_next_month' => $expiringNextMonth,
                'expiring_next_3_months' => $expiringNext3Months,
                'expiring_next_6_months' => $expiringNext6Months,
            ],
            'medicines' => $medicines,
        ]);
    }
}
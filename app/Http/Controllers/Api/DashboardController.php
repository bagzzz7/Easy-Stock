<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Models\Sale;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Return dashboard stats for the mobile app.
     */
    public function stats(): JsonResponse
    {
        $totalMedicines = Medicine::count();
        $lowStock       = Medicine::where('quantity', '<=', DB::raw('reorder_level'))
                                  ->where('status', '!=', 'expired')
                                  ->count();
        $outOfStock     = Medicine::where('status', 'out_of_stock')->count();
        $expiringSoon   = Medicine::where('expiry_date', '>', now())
                                  ->where('expiry_date', '<=', now()->addDays(60))
                                  ->where('status', '!=', 'expired')
                                  ->count();
        $expired        = Medicine::where('status', 'expired')->count();

        $todaySales = Sale::whereDate('created_at', today())
                          ->sum('grand_total');

        $monthSales = Sale::whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year)
                          ->sum('grand_total');

        $todayTransactions = Sale::whereDate('created_at', today())->count();

        return response()->json([
            'total_medicines'    => $totalMedicines,
            'low_stock'          => $lowStock,
            'out_of_stock'       => $outOfStock,
            'expiring_soon'      => $expiringSoon,
            'expired'            => $expired,
            'today_sales'        => round($todaySales, 2),
            'month_sales'        => round($monthSales, 2),
            'today_transactions' => $todayTransactions,
            'updated_at'         => now()->format('h:i A'),
        ]);
    }
}
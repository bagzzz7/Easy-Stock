<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\Sale;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalMedicines = Medicine::count();
        $lowStockCount = Medicine::where('quantity', '<=', DB::raw('reorder_level'))
                                ->where('status', '!=', 'expired')
                                ->count();
        $expiredCount = Medicine::where('status', 'expired')->count();
        $totalSuppliers = Supplier::count();
        
        $todaySales = Sale::whereDate('created_at', Carbon::today())->sum('grand_total');
        $monthSales = Sale::whereMonth('created_at', Carbon::now()->month)
                         ->whereYear('created_at', Carbon::now()->year)
                         ->sum('grand_total');
        
        $recentSales = Sale::with('user')
                          ->latest()
                          ->limit(10)
                          ->get();
        
        $lowStockMedicines = Medicine::where('quantity', '<=', DB::raw('reorder_level'))
                                    ->where('status', '!=', 'expired')
                                    ->limit(5)
                                    ->get();
        
        $expiringSoon = Medicine::where('expiry_date', '>', now())
                               ->where('expiry_date', '<=', now()->addDays(30))
                               ->limit(5)
                               ->get();
        
        // ✅ FIX: Create chart data array
        $chartData = $this->getSalesChartData();
        
        return view('dashboard', compact(
            'totalMedicines',
            'lowStockCount',
            'expiredCount',
            'totalSuppliers',
            'todaySales',
            'monthSales',
            'recentSales',
            'lowStockMedicines',
            'expiringSoon',
            'chartData' // ✅ Make sure this is included
        ));
    }

    // ✅ ADD THIS METHOD
    private function getSalesChartData()
    {
        $months = [];
        $salesData = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('M Y');
            
            $sales = Sale::whereYear('created_at', $date->year)
                        ->whereMonth('created_at', $date->month)
                        ->sum('grand_total');
            
            $salesData[] = $sales;
        }
        
        return [
            'months' => $months,
            'sales' => $salesData
        ];
    }

    public function getStats()
    {
        return response()->json([
            'total_medicines' => Medicine::count(),
            'low_stock' => Medicine::where('quantity', '<=', DB::raw('reorder_level'))
                                   ->where('status', '!=', 'expired')
                                   ->count(),
            'today_sales' => '₱' . number_format(Sale::whereDate('created_at', Carbon::today())->sum('grand_total'), 2),
            'month_sales' => '₱' . number_format(Sale::whereMonth('created_at', Carbon::now()->month)
                                                     ->whereYear('created_at', Carbon::now()->year)
                                                     ->sum('grand_total'), 2),
            'updated_at' => now()->format('h:i A')
        ]);
    }
}
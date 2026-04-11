<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Medicine;
use App\Models\Sale;

class LandingController extends Controller
{
    public function index()
    {
        // You can still pass data if needed
        $totalMedicines = Medicine::count();
        $totalSales = Sale::count();
        $happyCustomers = $totalSales * 10; // Estimate
        $yearsOfService = 4;
        
        return view('landing', compact(
            'totalMedicines',
            'totalSales',
            'happyCustomers',
            'yearsOfService'
        ));
    }
    
    public function features()
    {
        return view('landing.features');
    }
    
    public function pricing()
    {
        return view('landing.pricing');
    }
    
    public function contact()
    {
        return view('landing.contact');
    }
    
    public function about()
    {
        return view('landing.about');
    }
    
    public function demo()
    {
        return view('landing.demo');
    }
}
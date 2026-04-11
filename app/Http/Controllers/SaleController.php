<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Medicine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $query = Sale::with('user');

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('invoice')) {
            $query->where('invoice_number', 'LIKE', "%{$request->invoice}%");
        }

        $sales = $query->latest()->paginate(15);

        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        $medicines = Medicine::where('status', '!=', 'expired')
            ->where('quantity', '>', 0)
            ->orderBy('name')
            ->get();

        return view('sales.create', compact('medicines'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.medicine_id' => 'required|exists:medicines,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:cash,card,mobile_payment',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        DB::beginTransaction();

        try {
            $sale = Sale::create([
                'user_id' => Auth::id(),
                'discount' => $request->discount ?? 0,
                'tax' => $request->tax ?? 0,
                'payment_method' => $request->payment_method,
                'notes' => $request->notes,
                'total_amount' => 0,
                'grand_total' => 0
            ]);

            $totalAmount = 0;

            foreach ($request->items as $item) {
                $medicine = Medicine::findOrFail($item['medicine_id']);

                if ($medicine->quantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for {$medicine->name}. Available: {$medicine->quantity}");
                }

                $subtotal = $medicine->selling_price * $item['quantity'];

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'medicine_id' => $medicine->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $medicine->selling_price,
                    'subtotal' => $subtotal
                ]);

                $medicine->decrement('quantity', $item['quantity']);
                $totalAmount += $subtotal;
            }

            $sale->update([
                'total_amount' => $totalAmount,
                'grand_total' => $totalAmount - $sale->discount + $sale->tax
            ]);

            DB::commit();

            return redirect()->route('sales.show', $sale)
                ->with('success', 'Sale completed successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function show(Sale $sale)
    {
        $sale->load(['user', 'items.medicine']);

        return view('sales.show', compact('sale'));
    }

    public function invoice(Sale $sale)
    {
        $sale->load(['user', 'items.medicine']);

        return view('sales.invoice', compact('sale'));
    }

    public function destroy(Sale $sale)
    {
        DB::beginTransaction();

        try {
            foreach ($sale->items as $item) {
                $item->medicine->increment('quantity', $item->quantity);
            }

            $sale->delete();

            DB::commit();

            return redirect()->route('sales.index')
                ->with('success', 'Sale deleted and stock restored.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error deleting sale: ' . $e->getMessage());
        }
    }

    public function dailyReport(Request $request)
    {
        $date = $request->date ?? now()->toDateString();

        $sales = Sale::whereDate('created_at', $date)
            ->with(['user', 'items.medicine'])
            ->get();

        $totalSales = $sales->sum('grand_total');
        $totalItems = $sales->sum(fn($sale) => $sale->items->sum('quantity'));

        return view('reports.daily', compact('sales', 'date', 'totalSales', 'totalItems'));
    }

    public function monthlyReport(Request $request)
    {
        $month = $request->month ?? now()->format('Y-m');
        [$year, $monthNum] = explode('-', $month);

        $sales = Sale::whereYear('created_at', $year)
            ->whereMonth('created_at', $monthNum)
            ->with('user')
            ->get();

        $dailyData = $sales->groupBy(fn($sale) => $sale->created_at->toDateString())
            ->map(fn($daySales) => [
                'count' => $daySales->count(),
                'total' => $daySales->sum('grand_total')
            ]);

        $topMedicines = DB::table('sale_items')
            ->join('medicines', 'sale_items.medicine_id', '=', 'medicines.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereYear('sales.created_at', $year)
            ->whereMonth('sales.created_at', $monthNum)
            ->select('medicines.name', DB::raw('SUM(sale_items.quantity) as total_quantity'))
            ->groupBy('medicines.id', 'medicines.name')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();

        return view('reports.monthly', compact('sales', 'year', 'monthNum', 'dailyData', 'topMedicines'));
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatbotController extends Controller
{
    public function process(Request $request)
    {
        try {
            $request->validate([
                'message' => 'required|string'
            ]);

            $userMessage = strtolower($request->message);

            // Low stock check
            if (strpos($userMessage, 'low stock') !== false || strpos($userMessage, 'reorder') !== false) {
                return $this->getLowStockMedicines();
            }

            // Expiring soon
            if (strpos($userMessage, 'expiring') !== false || strpos($userMessage, 'expiry') !== false) {
                return $this->getExpiringMedicines();
            }

            // Sales summary
            if (strpos($userMessage, 'sales') !== false || strpos($userMessage, 'revenue') !== false) {
                return $this->getSalesData($userMessage);
            }

            // Specific medicine stock
            $medicine = Medicine::where('name', 'LIKE', "%{$userMessage}%")->first();
            if ($medicine) {
                return $this->checkMedicineStock($medicine);
            }

            // Help message
            return response()->json([
                'success' => true,
                'message' => "I'm your EasyStock AI assistant. Here are some things you can ask me:\n\n" .
                           "• What medicines are low in stock?\n" .
                           "• Show me expiring medicines\n" .
                           "• Today's sales summary\n" .
                           "• How many Paracetamol do we have?"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred. Please try again.'
            ], 500);
        }
    }

    private function getLowStockMedicines()
    {
        $lowStock = Medicine::where('quantity', '<=', DB::raw('reorder_level'))
            ->where('status', '!=', 'expired')
            ->orderBy('quantity', 'asc')
            ->get();

        if ($lowStock->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => "✅ Good news! No medicines are currently low in stock."
            ]);
        }

        $response = "⚠️ **Low Stock Alert**\n\n";
        foreach ($lowStock as $medicine) {
            $response .= "• {$medicine->name}: {$medicine->quantity} {$medicine->unit} (Reorder at {$medicine->reorder_level})\n";
        }

        return response()->json([
            'success' => true,
            'message' => $response
        ]);
    }

    private function getExpiringMedicines()
    {
        $expiringSoon = Medicine::where('expiry_date', '>', now())
            ->where('expiry_date', '<=', now()->addDays(60))
            ->orderBy('expiry_date', 'asc')
            ->get();

        if ($expiringSoon->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => "✅ No medicines are expiring in the next 60 days."
            ]);
        }

        $response = "⏰ **Medicines Expiring Soon**\n\n";
        foreach ($expiringSoon as $medicine) {
            $days = now()->diffInDays($medicine->expiry_date);
            $response .= "• {$medicine->name}: Expires {$medicine->expiry_date->format('M d, Y')} ({$days} days left)\n";
        }

        return response()->json([
            'success' => true,
            'message' => $response
        ]);
    }

    private function getSalesData($message)
    {
        if (strpos($message, 'today') !== false) {
            $sales = Sale::whereDate('created_at', today())->get();
            $period = "Today";
        } else {
            $sales = Sale::latest()->take(10)->get();
            $period = "Recent";
        }

        $totalRevenue = $sales->sum('grand_total');
        $totalCount = $sales->count();

        $response = "📊 **{$period} Sales Summary**\n\n";
        $response .= "• Total Transactions: {$totalCount}\n";
        $response .= "• Total Revenue: ₱" . number_format($totalRevenue, 2) . "\n";
        
        if ($totalCount > 0) {
            $response .= "• Average Sale: ₱" . number_format($totalRevenue / $totalCount, 2) . "\n";
        }

        return response()->json([
            'success' => true,
            'message' => $response
        ]);
    }

    private function checkMedicineStock($medicine)
    {
        $response = "🔍 **{$medicine->name}**\n" .
                   "• Stock: {$medicine->quantity} {$medicine->unit}\n" .
                   "• Price: ₱" . number_format($medicine->selling_price, 2) . "\n" .
                   "• Expiry: {$medicine->expiry_date->format('M d, Y')}\n" .
                   "• Status: " . ucfirst(str_replace('_', ' ', $medicine->status));

        return response()->json([
            'success' => true,
            'message' => $response
        ]);
    }
}
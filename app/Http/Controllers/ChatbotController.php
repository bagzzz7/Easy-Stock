<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\Sale;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class ChatbotController extends Controller
{
    private $apiKey;
    private $modelNames = [
        'llama-3.3-70b-versatile',
        'llama-3.1-8b-instant',
        'mixtral-8x7b-32768'
    ];

    public function __construct()
    {
        $this->apiKey = config('services.groq.api_key');
    }

    public function process(Request $request)
    {
        try {
            $request->validate([
                'message' => 'required|string'
            ]);

            $userMessage = strtolower($request->message);

            // First, check if this is a system query about inventory
            $systemResponse = $this->handleSystemQueries($userMessage);

            if ($systemResponse) {
                return response()->json([
                    'success' => true,
                    'message' => $systemResponse,
                    'disclaimer' => '⚠️ This information is from your pharmacy inventory system.'
                ]);
            }

            // If not a system query, use AI for general medicine questions
            return $this->handleAIQuery($userMessage, $request);

        } catch (\Exception $e) {
            Log::error('Chatbot error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred. Please try again.'
            ], 500);
        }
    }

    /**
     * Handle queries about the pharmacy system data
     */
    private function handleSystemQueries($message)
    {
        // 1. Low stock FIRST - before any general stock check
        if (preg_match('/(low|reorder|running low|almost empty|out of stock|short)/i', $message) &&
            preg_match('/(stock|supply|medicine|medicines|inventory)/i', $message)) {
            return $this->getLowStockMedicines();
        }

        // 2. Expiring soon
        if (preg_match('/(expiring|expiry|expiration|about to expire|expire soon)/i', $message)) {
            return $this->getExpiringMedicines();
        }

        // 3. Already expired
        if (preg_match('/(expired|already expired|past expiry)/i', $message)) {
            return $this->getExpiredMedicines();
        }

        // 4. Sales
        if (preg_match('/(sales|revenue|earnings|transactions)/i', $message)) {
            return $this->getSalesData($message);
        }

        // 5. Top selling
        if (preg_match('/(top selling|best selling|most sold|popular|best seller)/i', $message)) {
            return $this->getTopSellingMedicines();
        }

        // 6. Suppliers
        if (preg_match('/(supplier|vendor|suppliers)/i', $message)) {
            return $this->getSupplierInfo();
        }

        // 7. Categories
        if (preg_match('/(categories|types of medicine|medicine types)/i', $message)) {
            return $this->getCategoryInfo();
        }

        // 8. Inventory value
        if (preg_match('/(total value|inventory value|stock worth|how much.*inventory|inventory.*worth)/i', $message)) {
            return $this->getTotalInventoryValue();
        }

        // 9. Specific medicine stock check LAST - only triggers on actual medicine names
        if (preg_match('/\b(paracetamol|amoxicillin|biogesic|ibuprofen|aspirin|mefenamic|cetirizine|omeprazole)\b/i', $message, $matches)) {
            return $this->checkMedicineStock($matches[1]);
        }

        return null;
    }

    /**
     * Check stock of specific medicine
     */
    private function checkMedicineStock($medicineName = null)
    {
        if (!$medicineName) {
            $lowStock = Medicine::where('quantity', '<=', DB::raw('reorder_level'))->count();
            $totalMedicines = Medicine::count();

            return "📊 **Inventory Summary**\n" .
                   "• Total Medicines: {$totalMedicines}\n" .
                   "• Low Stock Items: {$lowStock}\n" .
                   "• In Stock: " . Medicine::where('status', 'in_stock')->count() . "\n" .
                   "• Out of Stock: " . Medicine::where('status', 'out_of_stock')->count() . "\n\n" .
                   "To check a specific medicine, ask: 'How many paracetamol do we have?'";
        }

        $medicines = Medicine::where('name', 'LIKE', "%{$medicineName}%")
            ->orWhere('generic_name', 'LIKE', "%{$medicineName}%")
            ->orWhere('brand', 'LIKE', "%{$medicineName}%")
            ->get();

        if ($medicines->isEmpty()) {
            return "I couldn't find any medicine matching '{$medicineName}' in our inventory.";
        }

        $response = "🔍 **Search Results for '{$medicineName}'**\n\n";

        foreach ($medicines as $medicine) {
            $status = $medicine->status;
            $statusEmoji = [
                'in_stock'     => '✅',
                'low_stock'    => '⚠️',
                'out_of_stock' => '❌',
                'expired'      => '⛔'
            ][$status] ?? '📦';

            $response .= "{$statusEmoji} **{$medicine->name}** ({$medicine->generic_name})\n";
            $response .= "   • Brand: {$medicine->brand}\n";
            $response .= "   • Stock: {$medicine->quantity_with_unit}\n";
            $response .= "   • Price: ₱" . number_format($medicine->selling_price, 2) . " per {$medicine->unit_display}\n";
            $response .= "   • Status: " . ucfirst(str_replace('_', ' ', $status)) . "\n";

            if ($medicine->expiry_date->isFuture()) {
                $daysLeft = $medicine->daysUntilExpiry();
                $response .= "   • Expires: {$medicine->expiry_date->format('M d, Y')} ({$daysLeft} days left)\n";
            } else {
                $response .= "   • ⚠️ EXPIRED: {$medicine->expiry_date->format('M d, Y')}\n";
            }
            $response .= "\n";
        }

        return $response;
    }

    /**
     * Get low stock medicines
     */
    private function getLowStockMedicines()
    {
        $lowStock = Medicine::with('category')
            ->where('quantity', '<=', DB::raw('reorder_level'))
            ->where('status', '!=', 'expired')
            ->orderBy('quantity', 'asc')
            ->get();

        if ($lowStock->isEmpty()) {
            return "✅ Good news! No medicines are currently low in stock.";
        }

        $critical = $lowStock->filter(function ($item) {
            return $item->quantity <= 5;
        });

        $warning = $lowStock->filter(function ($item) {
            return $item->quantity > 5 && $item->quantity <= $item->reorder_level;
        });

        $response = "⚠️ **Low Stock Alert**\n\n";

        if ($critical->isNotEmpty()) {
            $response .= "🔴 **CRITICAL STOCK (≤5 units)**\n";
            foreach ($critical as $medicine) {
                $response .= "   • {$medicine->name}: {$medicine->quantity_with_unit} (Reorder at {$medicine->reorder_level_with_unit})\n";
            }
            $response .= "\n";
        }

        if ($warning->isNotEmpty()) {
            $response .= "🟡 **NEEDS REORDER**\n";
            foreach ($warning as $medicine) {
                $response .= "   • {$medicine->name}: {$medicine->quantity_with_unit} (Reorder at {$medicine->reorder_level_with_unit})\n";
            }
        }

        $response .= "\n💡 Ask me to 'restock [medicine name]' to add inventory.";

        return $response;
    }

    /**
     * Get expiring medicines
     */
    private function getExpiringMedicines()
    {
        $expiringSoon = Medicine::where('expiry_date', '>', now())
            ->where('expiry_date', '<=', now()->addDays(60))
            ->where('status', '!=', 'expired')
            ->orderBy('expiry_date', 'asc')
            ->get();

        if ($expiringSoon->isEmpty()) {
            return "✅ No medicines are expiring in the next 60 days.";
        }

        $critical = $expiringSoon->filter(function ($item) {
            return $item->daysUntilExpiry() <= 30;
        });

        $warning = $expiringSoon->filter(function ($item) {
            $days = $item->daysUntilExpiry();
            return $days > 30 && $days <= 60;
        });

        $response = "⏰ **Medicines Expiring Soon**\n\n";

        if ($critical->isNotEmpty()) {
            $response .= "🔴 **EXPIRING WITHIN 30 DAYS**\n";
            foreach ($critical as $medicine) {
                $days = $medicine->daysUntilExpiry();
                $response .= "   • {$medicine->name}: {$medicine->quantity_with_unit} - Expires {$medicine->expiry_date->format('M d, Y')} ({$days} days left)\n";
            }
            $response .= "\n";
        }

        if ($warning->isNotEmpty()) {
            $response .= "🟡 **EXPIRING WITHIN 60 DAYS**\n";
            foreach ($warning as $medicine) {
                $days = $medicine->daysUntilExpiry();
                $response .= "   • {$medicine->name}: {$medicine->quantity_with_unit} - Expires {$medicine->expiry_date->format('M d, Y')} ({$days} days left)\n";
            }
        }

        return $response;
    }

    /**
     * Get expired medicines
     */
    private function getExpiredMedicines()
    {
        $expired = Medicine::where('expiry_date', '<', now())
            ->where('status', 'expired')
            ->orderBy('expiry_date', 'desc')
            ->get();

        if ($expired->isEmpty()) {
            return "✅ No expired medicines found in inventory.";
        }

        $response   = "⛔ **Expired Medicines**\n\n";
        $totalValue = 0;

        foreach ($expired as $medicine) {
            $value      = $medicine->quantity * $medicine->purchase_price;
            $totalValue += $value;
            $response   .= "   • {$medicine->name}: {$medicine->quantity_with_unit} - Expired {$medicine->expiry_date->format('M d, Y')}\n";
        }

        $response .= "\n💰 Total loss value: ₱" . number_format($totalValue, 2);

        return $response;
    }

    /**
     * Get sales data
     */
    private function getSalesData($message)
    {
        $thisWeek  = now()->startOfWeek();
        $thisMonth = now()->startOfMonth();

        if (strpos($message, 'today') !== false) {
            $sales  = Sale::whereDate('created_at', today())->get();
            $period = "Today";
        } elseif (strpos($message, 'week') !== false) {
            $sales  = Sale::where('created_at', '>=', $thisWeek)->get();
            $period = "This Week";
        } elseif (strpos($message, 'month') !== false) {
            $sales  = Sale::where('created_at', '>=', $thisMonth)->get();
            $period = "This Month";
        } else {
            $sales  = Sale::latest()->take(10)->get();
            $period = "Recent";
        }

        $totalSales   = $sales->count();
        $totalRevenue = $sales->sum('grand_total');
        $averageSale  = $totalSales > 0 ? $totalRevenue / $totalSales : 0;

        $response  = "📊 **{$period} Sales Summary**\n\n";
        $response .= "• Total Transactions: {$totalSales}\n";
        $response .= "• Total Revenue: ₱" . number_format($totalRevenue, 2) . "\n";
        $response .= "• Average Sale: ₱" . number_format($averageSale, 2) . "\n";

        if ($sales->isNotEmpty()) {
            $response .= "\n**Latest Transactions:**\n";
            foreach ($sales->take(5) as $sale) {
                $response .= "   • #{$sale->invoice_number}: ₱" . number_format($sale->grand_total, 2) .
                             " ({$sale->created_at->format('M d, h:i A')})\n";
            }
        }

        return $response;
    }

    /**
     * Get top selling medicines
     */
    private function getTopSellingMedicines()
    {
        $topMedicines = DB::table('sale_items')
            ->join('medicines', 'sale_items.medicine_id', '=', 'medicines.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.created_at', '>=', now()->subDays(30))
            ->select(
                'medicines.id',
                'medicines.name',
                'medicines.generic_name',
                DB::raw('SUM(sale_items.quantity) as total_quantity'),
                DB::raw('SUM(sale_items.subtotal) as total_revenue')
            )
            ->groupBy('medicines.id', 'medicines.name', 'medicines.generic_name')
            ->orderBy('total_quantity', 'desc')
            ->limit(10)
            ->get();

        if ($topMedicines->isEmpty()) {
            return "No sales data available for the last 30 days.";
        }

        $response = "🏆 **Top Selling Medicines (Last 30 Days)**\n\n";

        foreach ($topMedicines as $index => $item) {
            $medal     = $index == 0 ? '🥇' : ($index == 1 ? '🥈' : ($index == 2 ? '🥉' : '   '));
            $response .= "{$medal} **{$item->name}**\n";
            $response .= "   • Quantity Sold: {$item->total_quantity}\n";
            $response .= "   • Revenue: ₱" . number_format($item->total_revenue, 2) . "\n\n";
        }

        return $response;
    }

    /**
     * Get supplier information
     */
    private function getSupplierInfo()
    {
        $suppliers = Supplier::withCount('medicines')->get();

        $response = "🏢 **Supplier Overview**\n\n";

        foreach ($suppliers as $supplier) {
            $contact   = $supplier->contact_person ?? 'N/A';
            $response .= "• **{$supplier->name}**\n";
            $response .= "   • Contact: {$contact}\n";
            $response .= "   • Phone: {$supplier->phone}\n";
            $response .= "   • Products: {$supplier->medicines_count} medicines\n\n";
        }

        return $response;
    }

    /**
     * Get category information
     */
    private function getCategoryInfo()
    {
        $categories = DB::table('categories')
            ->leftJoin('medicines', 'categories.id', '=', 'medicines.category_id')
            ->select(
                'categories.id',
                'categories.name',
                DB::raw('COUNT(medicines.id) as medicine_count'),
                DB::raw('SUM(medicines.quantity) as total_stock')
            )
            ->groupBy('categories.id', 'categories.name')
            ->get();

        $response = "📑 **Medicine Categories**\n\n";

        foreach ($categories as $category) {
            $response .= "• **{$category->name}**: {$category->medicine_count} medicines";
            if ($category->total_stock) {
                $response .= " (Total stock: {$category->total_stock} units)";
            }
            $response .= "\n";
        }

        return $response;
    }

    /**
     * Get total inventory value
     */
    private function getTotalInventoryValue()
    {
        $totalValue = Medicine::select(DB::raw('SUM(quantity * purchase_price) as total_cost'))
            ->where('status', '!=', 'expired')
            ->first();

        $totalRetail = Medicine::select(DB::raw('SUM(quantity * selling_price) as total_retail'))
            ->where('status', '!=', 'expired')
            ->first();

        $costValue   = $totalValue->total_cost ?? 0;
        $retailValue = $totalRetail->total_retail ?? 0;
        $profit      = $retailValue - $costValue;

        $response  = "💰 **Inventory Value Report**\n\n";
        $response .= "• Total Cost Value: ₱" . number_format($costValue, 2) . "\n";
        $response .= "• Total Retail Value: ₱" . number_format($retailValue, 2) . "\n";
        $response .= "• Potential Profit: ₱" . number_format($profit, 2) . "\n\n";

        $totalItems = Medicine::where('status', '!=', 'expired')->sum('quantity');
        $response  .= "• Total Items in Stock: {$totalItems} units";

        return $response;
    }

    /**
     * Handle AI queries for general medicine questions
     */
    private function handleAIQuery($userMessage, $request)
    {
        $history = Session::get('chat_history', []);

        $messages = [
            [
                'role'    => 'system',
           'content' => 'You are EasyStock AI, an expert pharmacy assistant for EasyStock Pharmacy in Bohol, Philippines.

            You are knowledgeable about ALL medicines — both branded and generic — even if they are not in the pharmacy inventory.

            RESPONSE FORMAT — Always follow this exact structure:
            1. EMPATHY FIRST — Acknowledge the symptom with a warm, caring response.
            Example: "Sorry to hear that you\'re experiencing a headache. Headaches can be uncomfortable and disrupt your daily activities."
            2. FOLLOW-UP — Ask one clarifying question if needed (e.g., how long, severity).
            3. RECOMMENDATION — Only recommend medicines AFTER showing empathy.
            - Generic name AND brand name (e.g., Paracetamol / Biogesic, Tempra)
            - Standard dosage and frequency
            - Whether a prescription is required
            4. DISCLAIMER — Always end with: "⚠️ Please consult a licensed pharmacist or doctor before taking any medication."

            OTHER RULES:
            - Always respond in English
            - For prescription medicines, always say: "🔒 This medicine requires a prescription."
            - Be concise, friendly, and professional
            - Prefer medicines commonly available in Philippine pharmacies
            - For stock, sales, expiry questions, remind the user to ask specifically (e.g. "How many Paracetamol do we have?")'
                            ]
                    ];

        foreach ($history as $exchange) {
            $messages[] = ['role' => 'user',      'content' => $exchange['user']];
            $messages[] = ['role' => 'assistant', 'content' => $exchange['assistant']];
        }

        $messages[] = ['role' => 'user', 'content' => $userMessage];

        foreach ($this->modelNames as $modelName) {
            try {
                $response = Http::timeout(30)->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type'  => 'application/json',
                ])->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model'       => $modelName,
                    'messages'    => $messages,
                    'temperature' => 0.3,
                    'max_tokens'  => 500,
                ]);

                if ($response->successful()) {
                    $result = $response->json();

                    if (isset($result['choices'][0]['message']['content'])) {
                        $answer = $result['choices'][0]['message']['content'];

                        $history[] = [
                            'user'      => $userMessage,
                            'assistant' => $answer
                        ];

                        if (count($history) > 5) {
                            array_shift($history);
                        }

                        Session::put('chat_history', $history);

                        return response()->json([
                            'success'    => true,
                            'message'    => $answer,
                            ]);
                    }
                }

            } catch (\Exception $e) {
                Log::warning("Model {$modelName} error: " . $e->getMessage());
                continue;
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Unable to process your request at this time.'
        ], 500);
    }
}
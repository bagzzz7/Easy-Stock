<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $sale->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Courier New', monospace;
            background: #fff;
            font-size: 12px;
            line-height: 1.4;
            padding: 20px;
        }
        
        .invoice-box {
            max-width: 80mm; /* Standard thermal paper width */
            margin: 0 auto;
            background: white;
            padding: 10px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px dashed #333;
            padding-bottom: 10px;
        }
        
        .header h1 {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        
        .header h2 {
            font-size: 14px;
            font-weight: normal;
            margin-bottom: 5px;
        }
        
        .header p {
            font-size: 11px;
            color: #555;
        }
        
        .info {
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #333;
        }
        
        .info table {
            width: 100%;
            font-size: 11px;
        }
        
        .info td {
            padding: 2px 0;
        }
        
        .info td:last-child {
            text-align: right;
        }
        
        .items {
            margin-bottom: 15px;
        }
        
        .items table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        
        .items th {
            text-align: left;
            border-bottom: 1px solid #333;
            padding: 5px 0;
            font-size: 11px;
        }
        
        .items td {
            padding: 3px 0;
        }
        
        .items td:last-child,
        .items th:last-child {
            text-align: right;
        }
        
        .items td:nth-child(2),
        .items th:nth-child(2) {
            text-align: center;
        }
        
        .items td:nth-child(3),
        .items th:nth-child(3) {
            text-align: right;
        }
        
        .totals {
            margin-bottom: 15px;
            padding-top: 5px;
            border-top: 2px dashed #333;
        }
        
        .totals table {
            width: 100%;
            font-size: 11px;
        }
        
        .totals td {
            padding: 2px 0;
        }
        
        .totals td:last-child {
            text-align: right;
            font-weight: bold;
        }
        
        .grand-total {
            font-size: 14px;
            font-weight: bold;
            border-top: 1px solid #333;
            border-bottom: 1px solid #333;
            margin-top: 5px;
            padding: 5px 0;
        }
        
        .grand-total td {
            padding: 5px 0;
        }
        
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 2px dashed #333;
            font-size: 10px;
        }
        
        .footer p {
            margin: 2px 0;
        }
        
        .thank-you {
            font-size: 12px;
            font-weight: bold;
            margin: 10px 0;
        }
        
        @media print {
            body {
                padding: 0;
            }
            
            .no-print {
                display: none;
            }
            
            .invoice-box {
                max-width: 100%;
                padding: 0;
            }
        }
        
        .print-btn {
            text-align: center;
            margin: 20px 0;
        }
        
        .print-btn button {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 10px 30px;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            margin: 0 5px;
        }
        
        .print-btn button:hover {
            background: #45a049;
        }
        
        .print-btn button.close-btn {
            background: #6c757d;
        }
        
        .print-btn button.close-btn:hover {
            background: #5a6268;
        }
        
        .text-muted {
            color: #666;
        }
        
        .small-text {
            font-size: 9px;
        }
    </style>
</head>
<body>
    <div class="print-btn no-print">
        <button onclick="window.print()">
            <i class="fas fa-print"></i> Print Invoice
        </button>
        <button onclick="window.close()" class="close-btn">
            <i class="fas fa-times"></i> Close
        </button>
    </div>
    
    <div class="invoice-box">
        <!-- Header -->
        <div class="header">
            <h1>{{ config('app.name', 'Pharmacy POS') }}</h1>
            <h2>SALES INVOICE</h2>
            <p>123 Pharmacy Street, City<br>Tel: (123) 456-7890</p>
        </div>
        
        <!-- Invoice Info -->
        <div class="info">
            <table>
                <tr>
                    <td>Invoice #:</td>
                    <td><strong>{{ $sale->invoice_number }}</strong></td>
                </tr>
                <tr>
                    <td>Date:</td>
                    <td>{{ $sale->created_at->format('M d, Y') }}</td>
                </tr>
                <tr>
                    <td>Time:</td>
                    <td>{{ $sale->created_at->format('h:i A') }}</td>
                </tr>
                <tr>
                    <td>Cashier:</td>
                    <td>{{ $sale->user->name }}</td>
                </tr>
                <tr>
                    <td>Payment:</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}</td>
                </tr>
            </table>
        </div>
        
        <!-- Items -->
        <div class="items">
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sale->items as $item)
                    <tr>
                        <td>
                            {{ Str::limit($item->medicine->name, 20) }}
                            @if($item->medicine->generic_name)
                            <br><small class="text-muted small-text">{{ Str::limit($item->medicine->generic_name, 15) }}</small>
                            @endif
                        </td>
                        <td>{{ $item->quantity }}</td>
                        <td>₱{{ number_format($item->unit_price, 2) }}</td>
                        <td>₱{{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Totals -->
        <div class="totals">
            <table>
                <tr>
                    <td>Subtotal:</td>
                    <td>₱{{ number_format($sale->total_amount, 2) }}</td>
                </tr>
                @if($sale->discount > 0)
                <tr>
                    <td>Discount:</td>
                    <td class="text-muted">-₱{{ number_format($sale->discount, 2) }}</td>
                </tr>
                @endif
                <tr>
                    <td>Tax:</td>
                    <td>₱{{ number_format($sale->tax, 2) }}</td>
                </tr>
                <tr class="grand-total">
                    <td>TOTAL:</td>
                    <td>₱{{ number_format($sale->grand_total, 2) }}</td>
                </tr>
            </table>
        </div>
        
        <!-- Amount in words (Fixed version without helper function) -->
        @php
            function numberToWords($num) {
                $num = (float)$num;
                $ones = array(
                    0 => 'Zero', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 
                    5 => 'Five', 6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
                    10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 
                    14 => 'Fourteen', 15 => 'Fifteen', 16 => 'Sixteen', 17 => 'Seventeen', 
                    18 => 'Eighteen', 19 => 'Nineteen'
                );
                $tens = array(
                    2 => 'Twenty', 3 => 'Thirty', 4 => 'Forty', 5 => 'Fifty', 
                    6 => 'Sixty', 7 => 'Seventy', 8 => 'Eighty', 9 => 'Ninety'
                );
                
                if ($num < 20) {
                    return $ones[$num];
                } elseif ($num < 100) {
                    return $tens[floor($num/10)] . (($num % 10 > 0) ? ' ' . $ones[$num % 10] : '');
                } elseif ($num < 1000) {
                    return $ones[floor($num/100)] . ' Hundred' . (($num % 100 > 0) ? ' ' . numberToWords($num % 100) : '');
                } elseif ($num < 1000000) {
                    return numberToWords(floor($num/1000)) . ' Thousand' . (($num % 1000 > 0) ? ' ' . numberToWords($num % 1000) : '');
                }
                return $num;
            }
            
            $whole = floor($sale->grand_total);
            $cents = round(($sale->grand_total - $whole) * 100);
            $words = numberToWords($whole);
            $centsText = $cents > 0 ? ' and ' . $cents . '/100' : '';
        @endphp
        
        <div style="margin-bottom: 10px; font-size: 10px;">
            <strong>Amount in words:</strong><br>
            {{ ucwords($words) }} Pesos{{ $centsText }}
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p class="thank-you">THANK YOU FOR YOUR PATRONAGE!</p>
            <p>This serves as your official receipt</p>
            <p style="font-size: 8px;">VAT REG TIN: 123-456-789-000</p>
            <p style="font-size: 8px;">POS #: 001 | Invoice #: {{ $sale->invoice_number }}</p>
            <p style="font-size: 8px;">{{ now()->format('Y-m-d h:i:s A') }}</p>
            <div style="margin-top: 5px;">
                {!! config('app.footer_text', '') !!}
            </div>
        </div>
    </div>

    <script>
        // Auto print dialog (uncomment if needed)
        // window.onload = function() {
        //     window.print();
        // };
    </script>
</body>
</html>
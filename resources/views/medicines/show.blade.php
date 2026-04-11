@extends('layouts.app')

@section('title', 'Medicine Details - ' . $medicine->name)

@section('content')
<div class="medicine-detail-modern">
    {{-- Header --}}
    <div class="detail-header">
        <div class="header-left">
            <div class="back-button">
                <a href="{{ route('medicines.index') }}" class="btn-back">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Medicines</span>
                </a>
            </div>
            <div class="medicine-badge">
                @if($medicine->requires_prescription)
                    <span class="badge-rx">Prescription Required (Rx)</span>
                @else
                    <span class="badge-otc">Over the Counter (OTC)</span>
                @endif
                <span class="badge-status status-{{ $medicine->status }}">
                    {{ str_replace('_', ' ', ucfirst($medicine->status)) }}
                </span>
            </div>
            <h1 class="medicine-name">{{ $medicine->name }}</h1>
            <p class="medicine-subtitle">{{ $medicine->generic_name }}</p>
            @if($medicine->brand)
                <p class="medicine-brand">{{ $medicine->brand }}</p>
            @endif
        </div>
        <div class="header-right">
            <a href="{{ route('medicines.edit', $medicine) }}" class="btn-edit">
                <i class="fas fa-edit"></i>
                <span>Edit Medicine</span>
            </a>
        </div>
    </div>

    {{-- Information Table --}}
    <div class="detail-card">
        <div class="card-header">
            <i class="fas fa-info-circle"></i>
            <h3>Medicine Information</h3>
        </div>
        <div class="card-body">
            <table class="info-table">
                <tr>
                    <th>Generic Name</th>
                    <td>{{ $medicine->generic_name }}</td>
                </tr>
                @if($medicine->brand)
                <tr>
                    <th>Brand</th>
                    <td>{{ $medicine->brand }}</td>
                </tr>
                @endif
                <tr>
                    <th>Category</th>
                    <td>
                        <span class="category-badge">{{ $medicine->category->name ?? 'Uncategorized' }}</span>
                    </td>
                </tr>
                @if($medicine->strength)
                <tr>
                    <th>Strength/Dosage</th>
                    <td>
                        <span class="strength-badge">{{ $medicine->strength }} {{ $medicine->unit }}</span>
                    </td>
                </tr>
                @endif
                <tr>
                    <th>Suitable For</th>
                    <td>
                        @php
                            $typeLabels = ['adults' => 'Adults Only', 'children' => 'Children Only', 'both' => 'Both Adults & Children'];
                            $typeColors = ['adults' => 'blue', 'children' => 'green', 'both' => 'teal'];
                        @endphp
                        @if($medicine->category_type)
                            <span class="age-badge age-{{ $typeColors[$medicine->category_type] }}">
                                {{ $typeLabels[$medicine->category_type] }}
                            </span>
                        @else
                            <span class="text-muted">Not specified</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Supplier</th>
                    <td>
                        @if($medicine->supplier)
                            <a href="{{ route('suppliers.show', $medicine->supplier) }}" class="supplier-link">
                                <i class="fas fa-truck"></i> {{ $medicine->supplier->name }}
                            </a>
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Batch Number</th>
                    <td><code>{{ $medicine->batch_number }}</code></td>
                </tr>
                <tr>
                    <th>Shelf Location</th>
                    <td>{{ $medicine->shelf_number ?? 'Not assigned' }}</td>
                </tr>
                <tr>
                    <th>Description</th>
                    <td>{{ $medicine->description ?? 'No description available' }}</td>
                </tr>
            </table>
        </div>
    </div>

    {{-- Stock & Pricing Table --}}
    <div class="detail-card">
        <div class="card-header">
            <i class="fas fa-boxes"></i>
            <h3>Stock & Pricing Information</h3>
        </div>
        <div class="card-body">
            <table class="info-table">
                <tr>
                    <th>Current Stock</th>
                    <td>
                        <div class="stock-display">
                            <span class="stock-number {{ $medicine->quantity == 0 ? 'critical' : ($medicine->status === 'low_stock' ? 'warning' : 'normal') }}">
                                {{ number_format($medicine->quantity) }} {{ $medicine->unit }}
                            </div>
                            <div class="stock-bar-large">
                                <div class="stock-fill" style="width: {{ $medicine->reorder_level > 0 ? min(100, ($medicine->quantity / ($medicine->reorder_level * 2)) * 100) : 100 }}%"></div>
                            </div>
                            <div class="stock-reorder-info">
                                Reorder Level: {{ $medicine->reorder_level }} {{ $medicine->unit }}
                                @if($medicine->isLowStock() && !$medicine->isExpired())
                                    <span class="alert-low"><i class="fas fa-exclamation-triangle"></i> Low Stock Alert!</span>
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>Purchase Price</th>
                    <td>₱{{ number_format($medicine->purchase_price, 2) }} <small class="text-muted">per {{ $medicine->unit }}</small></td>
                </tr>
                <tr>
                    <th>Selling Price</th>
                    <td class="highlight">₱{{ number_format($medicine->selling_price, 2) }} <small class="text-muted">per {{ $medicine->unit }}</small></td>
                </tr>
                <tr>
                    <th>Profit Margin</th>
                    <td class="success">₱{{ number_format($medicine->profit_amount, 2) }} <small class="text-muted">({{ $medicine->profit_margin }}% markup)</small></td>
                </tr>
                <tr>
                    <th>Expiry Date</th>
                    <td>
                        <div class="expiry-info {{ $medicine->isExpired() ? 'expired' : ($medicine->daysUntilExpiry() <= 30 ? 'expiring' : '') }}">
                            <strong>{{ $medicine->expiry_date->format('F d, Y') }}</strong>
                            @php $daysLeft = $medicine->daysUntilExpiry(); @endphp
                            @if($medicine->isExpired())
                                <span class="expiry-status expired">Expired {{ abs($daysLeft) }} days ago</span>
                            @else
                                <span class="expiry-status {{ $daysLeft <= 30 ? 'warning' : 'good' }}">
                                    {{ $daysLeft }} days remaining
                                </span>
                            @endif
                        </div>
                        @if($medicine->isExpired())
                            <div class="alert-expired">
                                <i class="fas fa-skull-crosswalk"></i>
                                This medicine has expired and should be disposed of immediately.
                            </div>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Total Stock Value</th>
                    <td class="success">₱{{ number_format($medicine->quantity * $medicine->selling_price, 2) }} <small class="text-muted">at selling price</small></td>
                </tr>
                <tr>
                    <th>Total Cost</th>
                    <td>₱{{ number_format($medicine->quantity * $medicine->purchase_price, 2) }} <small class="text-muted">at purchase price</small></td>
                </tr>
                <tr>
                    <th>Potential Profit</th>
                    <td class="success">₱{{ number_format(($medicine->quantity * $medicine->selling_price) - ($medicine->quantity * $medicine->purchase_price), 2) }}</td>
                </tr>
            </table>
        </div>
    </div>

    {{-- Sales History Table --}}
    <div class="detail-card">
        <div class="card-header">
            <i class="fas fa-history"></i>
            <h3>Recent Sales History</h3>
        </div>
        <div class="card-body">
            @if($medicine->saleItems->isEmpty())
                <div class="empty-sales">
                    <i class="fas fa-shopping-cart"></i>
                    <p>No sales recorded for this medicine yet.</p>
                </div>
            @else
                <div class="sales-table-container">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Invoice #</th>
                                <th>Customer</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($medicine->saleItems as $item)
                            <tr>
                                <td>
                                    <div class="date-cell">
                                        <span>{{ $item->sale->created_at->format('M d, Y') }}</span>
                                        <small>{{ $item->sale->created_at->format('h:i A') }}</small>
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ route('sales.show', $item->sale) }}" class="invoice-link">
                                        {{ $item->sale->invoice_number }}
                                    </a>
                                \n
                                <td>{{ $item->sale->customer_name ?? 'Walk-in Customer' }}</td>
                                <td class="quantity-cell">
                                    <span class="quantity-badge">{{ $item->quantity }}</span>
                                    <small>{{ $medicine->unit }}</small>
                                </td>
                                <td>₱{{ number_format($item->unit_price, 2) }}</td>
                                <td class="total-cell">₱{{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Medicine Detail Modern CSS - Table View */
.medicine-detail-modern {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

/* Header */
.detail-header {
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    border-radius: 24px;
    padding: 2rem;
    margin-bottom: 2rem;
    color: white;
    position: relative;
    overflow: hidden;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

.detail-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(37, 99, 235, 0.1) 0%, transparent 70%);
    pointer-events: none;
}

.header-left {
    position: relative;
    z-index: 1;
    flex: 1;
}

.header-right {
    position: relative;
    z-index: 1;
}

.back-button {
    margin-bottom: 1rem;
}

.btn-back {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: rgba(255, 255, 255, 0.7);
    text-decoration: none;
    font-size: 0.875rem;
    transition: all 0.2s;
}

.btn-back:hover {
    color: white;
    transform: translateX(-4px);
}

.medicine-badge {
    display: flex;
    gap: 0.75rem;
    margin-bottom: 1rem;
    flex-wrap: wrap;
}

.badge-rx, .badge-otc {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}

.badge-rx {
    background: #ef4444;
    color: white;
}

.badge-otc {
    background: #10b981;
    color: white;
}

.badge-status {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}

.medicine-name {
    font-size: 2rem;
    font-weight: 700;
    margin: 0 0 0.5rem 0;
}

.medicine-subtitle {
    font-size: 1rem;
    color: rgba(255, 255, 255, 0.7);
    margin: 0;
}

.medicine-brand {
    font-size: 0.875rem;
    color: rgba(255, 255, 255, 0.5);
    margin: 0.25rem 0 0 0;
}

.btn-edit {
    padding: 0.625rem 1.25rem;
    border-radius: 40px;
    font-weight: 600;
    font-size: 0.875rem;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s;
    background: #f59e0b;
    color: white;
}

.btn-edit:hover {
    background: #d97706;
    transform: translateY(-2px);
}

/* Cards */
.detail-card {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 1px solid #e2e8f0;
    margin-bottom: 1.5rem;
}

.card-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1.25rem 1.5rem;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
}

.card-header i {
    font-size: 1.25rem;
    color: #2563eb;
}

.card-header h3 {
    margin: 0;
    font-size: 1rem;
    font-weight: 600;
    color: #0f172a;
}

.card-body {
    padding: 1.5rem;
}

/* Info Tables */
.info-table {
    width: 100%;
    border-collapse: collapse;
}

.info-table th {
    width: 200px;
    padding: 1rem;
    text-align: left;
    font-weight: 600;
    color: #475569;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
}

.info-table td {
    padding: 1rem;
    color: #0f172a;
    border-bottom: 1px solid #e2e8f0;
}

.info-table tr:last-child th,
.info-table tr:last-child td {
    border-bottom: none;
}

.category-badge, .strength-badge {
    background: #eff6ff;
    color: #2563eb;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
}

.age-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}

.age-blue { background: #dbeafe; color: #1e40af; }
.age-green { background: #dcfce7; color: #166534; }
.age-teal { background: #ccfbf1; color: #0f766e; }

.supplier-link {
    color: #2563eb;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
}

.supplier-link:hover {
    text-decoration: underline;
}

/* Stock Display */
.stock-display {
    margin-bottom: 0.5rem;
}

.stock-number {
    font-size: 1.5rem;
    font-weight: 800;
}

.stock-number.critical { color: #ef4444; }
.stock-number.warning { color: #f59e0b; }
.stock-number.normal { color: #10b981; }

.stock-bar-large {
    height: 6px;
    background: #e2e8f0;
    border-radius: 3px;
    overflow: hidden;
    margin: 0.75rem 0;
    max-width: 300px;
}

.stock-fill {
    height: 100%;
    background: #10b981;
    border-radius: 3px;
    transition: width 0.3s;
}

.stock-reorder-info {
    font-size: 0.75rem;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.alert-low {
    color: #f59e0b;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
}

.highlight {
    font-weight: 700;
    color: #2563eb;
    font-size: 1.1rem;
}

.success {
    font-weight: 700;
    color: #10b981;
    font-size: 1.1rem;
}

.text-muted {
    font-size: 0.7rem;
    font-weight: normal;
    color: #94a3b8;
}

/* Expiry Info */
.expiry-info {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 6px;
}

.expiry-info.expired {
    background: #fee2e2;
    color: #991b1b;
}

.expiry-info.expiring {
    background: #fed7aa;
    color: #9a3412;
}

.expiry-status {
    margin-left: 0.5rem;
    font-size: 0.7rem;
}

.expiry-status.expired { color: #ef4444; }
.expiry-status.warning { color: #f59e0b; }
.expiry-status.good { color: #10b981; }

.alert-expired {
    margin-top: 0.75rem;
    padding: 0.75rem;
    background: #fee2e2;
    border-radius: 8px;
    color: #991b1b;
    font-size: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* Sales Table */
.sales-table-container {
    overflow-x: auto;
}

.modern-table {
    width: 100%;
    border-collapse: collapse;
}

.modern-table thead {
    background: #f8fafc;
}

.modern-table th {
    padding: 0.75rem 1rem;
    text-align: left;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #64748b;
    border-bottom: 1px solid #e2e8f0;
}

.modern-table td {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #f1f5f9;
}

.modern-table tbody tr:hover {
    background: #f8fafc;
}

.date-cell span {
    display: block;
    font-weight: 500;
}

.date-cell small {
    font-size: 0.7rem;
    color: #94a3b8;
}

.invoice-link {
    color: #2563eb;
    text-decoration: none;
    font-weight: 500;
}

.invoice-link:hover {
    text-decoration: underline;
}

.quantity-badge {
    background: #e2e8f0;
    padding: 0.125rem 0.375rem;
    border-radius: 4px;
    font-weight: 600;
    font-size: 0.75rem;
}

.quantity-cell small {
    font-size: 0.65rem;
    color: #94a3b8;
    margin-left: 0.25rem;
}

.total-cell {
    font-weight: 700;
    color: #10b981;
}

.empty-sales {
    text-align: center;
    padding: 3rem;
    color: #94a3b8;
}

.empty-sales i {
    font-size: 3rem;
    margin-bottom: 1rem;
}

/* Responsive */
@media (max-width: 768px) {
    .medicine-detail-modern {
        padding: 1rem;
    }
    
    .detail-header {
        flex-direction: column;
        gap: 1rem;
    }
    
    .header-right {
        align-self: flex-start;
    }
    
    .info-table th {
        width: 120px;
        padding: 0.75rem;
    }
    
    .info-table td {
        padding: 0.75rem;
    }
    
    .modern-table {
        font-size: 0.75rem;
    }
    
    .modern-table td, 
    .modern-table th {
        padding: 0.5rem;
    }
}
</style>
@endpush
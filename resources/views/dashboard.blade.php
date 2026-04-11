@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="dashboard-modern">
    {{-- Hero Section --}}
    <div class="dash-hero">
        <div class="dash-hero-content">
            <div class="dash-hero-left">
                <div class="dash-badge">Welcome back, {{ Auth::user()->name }}!</div>
                <h1 class="dash-title">Dashboard</h1>
                <p class="dash-subtitle">Your pharmacy management overview at a glance</p>
            </div>
            <div class="dash-hero-right">
                <div class="date-chip">
                    <i class="fas fa-calendar-alt"></i>
                    <span>{{ now()->format('l, F j, Y') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Modern KPI Cards --}}
    <div class="kpi-grid-dash">
        <div class="kpi-card-dash" data-type="medicines">
            <div class="kpi-icon-dash blue">
                <i class="fas fa-pills"></i>
            </div>
            <div class="kpi-info-dash">
                <h3 id="totalMedicines">{{ $totalMedicines }}</h3>
                <p>Total Medicines</p>
                <span class="trend-dash">Active inventory</span>
            </div>
            <a href="{{ route('medicines.index') }}" class="kpi-link">
                <i class="fas fa-arrow-right"></i>
                <span>View all</span>
            </a>
        </div>

        <div class="kpi-card-dash" data-type="lowstock">
            <div class="kpi-icon-dash warning">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="kpi-info-dash">
                <h3 id="lowStock">{{ $lowStockCount }}</h3>
                <p>Low Stock</p>
                <span class="trend-dash danger">Needs attention</span>
            </div>
            <a href="{{ route('medicines.low-stock') }}" class="kpi-link">
                <i class="fas fa-arrow-right"></i>
                <span>View low stock</span>
            </a>
        </div>

        <div class="kpi-card-dash" data-type="today">
            <div class="kpi-icon-dash green">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div class="kpi-info-dash">
                <h3 id="todaySales">₱{{ number_format($todaySales, 2) }}</h3>
                <p>Today's Sales</p>
                <span class="trend-dash">{{ now()->format('M d, Y') }}</span>
            </div>
            <a href="{{ route('sales.index', ['date_from' => now()->format('Y-m-d'), 'date_to' => now()->format('Y-m-d')]) }}" class="kpi-link">
                <i class="fas fa-arrow-right"></i>
                <span>View sales</span>
            </a>
        </div>

        <div class="kpi-card-dash" data-type="monthly">
            <div class="kpi-icon-dash purple">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="kpi-info-dash">
                <h3 id="monthSales">₱{{ number_format($monthSales, 2) }}</h3>
                <p>Monthly Sales</p>
                <span class="trend-dash">{{ now()->format('F Y') }}</span>
            </div>
            <a href="{{ route('sales.index', ['date_from' => now()->startOfMonth()->format('Y-m-d'), 'date_to' => now()->format('Y-m-d')]) }}" class="kpi-link">
                <i class="fas fa-arrow-right"></i>
                <span>View report</span>
            </a>
        </div>
    </div>

    {{-- Charts and Quick Actions Row --}}
    <div class="two-column-grid">
        {{-- Sales Chart --}}
        <div class="chart-card">
            <div class="card-header-modern">
                <div class="header-left">
                    <i class="fas fa-chart-line"></i>
                    <h3>Sales Trend</h3>
                </div>
                <div class="header-right">
                    <span class="period-badge">Last 6 Months</span>
                </div>
            </div>
            <div class="card-body-modern">
                <div class="chart-container">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="actions-card">
            <div class="card-header-modern">
                <div class="header-left">
                    <i class="fas fa-bolt"></i>
                    <h3>Quick Actions</h3>
                </div>
            </div>
            <div class="card-body-modern">
                <div class="actions-grid">
                    <a href="{{ route('medicines.create') }}" class="action-btn primary">
                        <i class="fas fa-plus-circle"></i>
                        <span>Add Medicine</span>
                    </a>
                    <a href="{{ route('sales.create') }}" class="action-btn success">
                        <i class="fas fa-cash-register"></i>
                        <span>New Sale</span>
                    </a>
                    @if(auth()->user()->isManagement())
                    <a href="{{ route('suppliers.create') }}" class="action-btn info">
                        <i class="fas fa-truck"></i>
                        <span>Add Supplier</span>
                    </a>
                    @endif
                    <a href="{{ route('reports.sales') }}" class="action-btn secondary">
                        <i class="fas fa-chart-bar"></i>
                        <span>Sales Report</span>
                    </a>
                    <a href="{{ route('inventory.index') }}" class="action-btn warning">
                        <i class="fas fa-warehouse"></i>
                        <span>Manage Stock</span>
                    </a>
                    <a href="{{ route('medicines.low-stock') }}" class="action-btn danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span>Low Stock Alert</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Alerts Section --}}
    <div class="alerts-section">
        <div class="alert-card">
            <div class="alert-header warning">
                <i class="fas fa-exclamation-triangle"></i>
                <h3>Low Stock Medicines</h3>
                <span class="badge-count">{{ $lowStockMedicines->count() }}</span>
            </div>
            <div class="alert-body">
                @if($lowStockMedicines->isEmpty())
                    <div class="empty-alert">
                        <i class="fas fa-check-circle"></i>
                        <p>All medicines are sufficiently stocked!</p>
                    </div>
                @else
                    <div class="alert-table">
                        <table class="modern-table">
                            <thead>
                                <tr>
                                    <th>Medicine</th>
                                    <th>Current Stock</th>
                                    <th>Reorder Level</th>
                                    <th>Action</th>
                                 </tr>
                            </thead>
                            <tbody>
                                @foreach($lowStockMedicines as $medicine)
                                <tr>
                                    <td>
                                        <div class="medicine-cell">
                                            <div class="medicine-icon warning-bg">
                                                <i class="fas fa-capsules"></i>
                                            </div>
                                            <div>
                                                <div class="medicine-name">{{ $medicine->name }}</div>
                                                <div class="medicine-meta">{{ $medicine->generic_name }}</div>
                                                @if($medicine->strength)
                                                    <div class="medicine-dose">{{ $medicine->strength }} {{ $medicine->unit }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="stock-badge critical">{{ $medicine->quantity }}</span>
                                    </td>
                                    <td>{{ $medicine->reorder_level }}</td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="{{ route('medicines.edit', $medicine) }}" class="action-icon edit" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('medicines.show', $medicine) }}" class="action-icon view" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button class="action-icon stock" onclick="quickStockIn({{ $medicine->id }},'{{ addslashes($medicine->name) }}',{{ $medicine->quantity }},'{{ $medicine->unit }}','{{ $medicine->batch_number }}',{{ $medicine->purchase_price }})" title="Stock In">
                                                <i class="fas fa-arrow-down"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="alert-footer">
                        <a href="{{ route('medicines.low-stock') }}" class="view-all-btn warning">
                            <i class="fas fa-exclamation-triangle"></i> View All Low Stock
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <div class="alert-card">
            <div class="alert-header danger">
                <i class="fas fa-clock"></i>
                <h3>Expiring Soon</h3>
                <span class="badge-count">{{ $expiringSoon->count() }}</span>
            </div>
            <div class="alert-body">
                @if($expiringSoon->isEmpty())
                    <div class="empty-alert">
                        <i class="fas fa-check-circle"></i>
                        <p>No medicines expiring soon!</p>
                    </div>
                @else
                    <div class="alert-table">
                        <table class="modern-table">
                            <thead>
                                 <tr>
                                    <th>Medicine</th>
                                    <th>Batch</th>
                                    <th>Expiry Date</th>
                                    <th>Days Left</th>
                                    <th>Action</th>
                                 </tr>
                            </thead>
                            <tbody>
                                @foreach($expiringSoon as $medicine)
                                @php
                                    $daysLeft = $medicine->expiry_date->diffInDays(now());
                                @endphp
                                <tr>
                                    <td>
                                        <div class="medicine-cell">
                                            <div class="medicine-icon danger-bg">
                                                <i class="fas fa-clock"></i>
                                            </div>
                                            <div>
                                                <div class="medicine-name">{{ $medicine->name }}</div>
                                                <div class="medicine-meta">{{ $medicine->generic_name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $medicine->batch_number }}</td>
                                    <td>{{ $medicine->expiry_date->format('M d, Y') }}</td>
                                    <td>
                                        <span class="expiry-badge {{ $daysLeft < 7 ? 'critical' : ($daysLeft < 15 ? 'warning' : 'good') }}">
                                            {{ $daysLeft }} days
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="{{ route('medicines.edit', $medicine) }}" class="action-icon edit" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('medicines.show', $medicine) }}" class="action-icon view" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="alert-footer">
                        <a href="{{ route('medicines.expiring') }}" class="view-all-btn danger">
                            <i class="fas fa-clock"></i> View All Expiring
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Recent Sales Section --}}
    <div class="sales-section">
        <div class="sales-header">
            <div class="header-left">
                <i class="fas fa-history"></i>
                <h3>Recent Sales</h3>
            </div>
            <a href="{{ route('sales.index') }}" class="view-all-btn primary">
                <i class="fas fa-shopping-cart"></i> View All Sales
            </a>
        </div>

        @if($recentSales->isEmpty())
            <div class="empty-state">
                <i class="fas fa-shopping-cart"></i>
                <h4>No recent sales</h4>
                <p>Start making sales to see them here</p>
                <a href="{{ route('sales.create') }}" class="btn-modern btn-primary">Create New Sale</a>
            </div>
        @else
            <div class="sales-table-container">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Date & Time</th>
                            <th>Cashier</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Payment</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentSales as $sale)
                        <tr>
                            <td>
                                <span class="invoice-number">#{{ $sale->invoice_number }}</span>
                            </td>
                            <td>
                                <div class="datetime-cell">
                                    <span>{{ $sale->created_at->format('M d, Y') }}</span>
                                    <small>{{ $sale->created_at->format('h:i A') }}</small>
                                </div>
                            </td>
                            <td>
                                <div class="cashier-info">
                                    <div class="cashier-avatar">{{ strtoupper(substr($sale->user->name, 0, 1)) }}</div>
                                    <span>{{ $sale->user->name }}</span>
                                </div>
                            </td>
                            <td>{{ $sale->item_count }} items</td>
                            <td class="total-cell">₱{{ number_format($sale->grand_total, 2) }}</td>
                            <td>
                                <span class="payment-badge payment-{{ $sale->payment_method }}">
                                    <i class="fas fa-{{ $sale->payment_method == 'cash' ? 'money-bill' : ($sale->payment_method == 'card' ? 'credit-card' : 'mobile-alt') }}"></i>
                                    {{ ucfirst($sale->payment_method) }}
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('sales.show', $sale) }}" class="action-icon view" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('sales.invoice', $sale) }}" target="_blank" class="action-icon print" title="Print Invoice">
                                        <i class="fas fa-print"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
/* Dashboard Modern CSS */
.dashboard-modern {
    max-width: 1600px;
    margin: 0 auto;
    padding: 2rem;
}

/* Hero Section */
.dash-hero {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    border-radius: 24px;
    padding: 2rem;
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
}

.dash-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(37, 99, 235, 0.1) 0%, transparent 70%);
    pointer-events: none;
}

.dash-hero-content {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    position: relative;
    z-index: 1;
}

.dash-badge {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 500;
    margin-bottom: 1rem;
    color: white;
}

.dash-title {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    color: white;
}

.dash-subtitle {
    color: rgba(255, 255, 255, 0.7);
    margin: 0;
}

.date-chip {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    padding: 0.5rem 1rem;
    border-radius: 40px;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    color: white;
}

/* KPI Grid */
.kpi-grid-dash {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.kpi-card-dash {
    background: white;
    border-radius: 20px;
    padding: 1.5rem;
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    transition: all 0.3s;
    border: 1px solid #e2e8f0;
    position: relative;
}

.kpi-card-dash:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.kpi-icon-dash {
    width: 56px;
    height: 56px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.kpi-icon-dash.blue { background: linear-gradient(135deg, #3b82f6, #1e40af); color: white; }
.kpi-icon-dash.green { background: linear-gradient(135deg, #10b981, #047857); color: white; }
.kpi-icon-dash.warning { background: linear-gradient(135deg, #f59e0b, #d97706); color: white; }
.kpi-icon-dash.purple { background: linear-gradient(135deg, #8b5cf6, #6d28d9); color: white; }

.kpi-info-dash {
    flex: 1;
}

.kpi-info-dash h3 {
    font-size: 1.75rem;
    font-weight: 700;
    margin: 0 0 0.25rem 0;
    color: #0f172a;
}

.kpi-info-dash p {
    margin: 0;
    font-size: 0.875rem;
    color: #64748b;
    font-weight: 500;
}

.trend-dash {
    font-size: 0.75rem;
    color: #94a3b8;
}

.trend-dash.danger { color: #ef4444; }

.kpi-link {
    position: absolute;
    bottom: 1rem;
    right: 1rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.7rem;
    color: #94a3b8;
    text-decoration: none;
    transition: all 0.2s;
}

.kpi-link:hover {
    color: #2563eb;
    gap: 0.5rem;
}

/* Two Column Grid */
.two-column-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.chart-card, .actions-card {
    background: white;
    border-radius: 20px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.card-header-modern {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.25rem 1.5rem;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
}

.card-header-modern .header-left {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.card-header-modern .header-left i {
    font-size: 1.25rem;
    color: #2563eb;
}

.card-header-modern .header-left h3 {
    margin: 0;
    font-size: 1rem;
    font-weight: 600;
    color: #0f172a;
}

.period-badge {
    background: #e2e8f0;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 500;
    color: #475569;
}

.card-body-modern {
    padding: 1.5rem;
}

.chart-container {
    position: relative;
    height: 300px;
}

/* Actions Grid */
.actions-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.75rem;
}

.action-btn {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    border-radius: 12px;
    text-decoration: none;
    transition: all 0.2s;
    font-weight: 500;
    font-size: 0.875rem;
}

.action-btn i {
    font-size: 1.25rem;
}

.action-btn.primary {
    background: #eff6ff;
    color: #2563eb;
}

.action-btn.primary:hover {
    background: #2563eb;
    color: white;
    transform: translateY(-2px);
}

.action-btn.success {
    background: #dcfce7;
    color: #10b981;
}

.action-btn.success:hover {
    background: #10b981;
    color: white;
    transform: translateY(-2px);
}

.action-btn.info {
    background: #e0f2fe;
    color: #0284c7;
}

.action-btn.info:hover {
    background: #0284c7;
    color: white;
    transform: translateY(-2px);
}

.action-btn.secondary {
    background: #f1f5f9;
    color: #475569;
}

.action-btn.secondary:hover {
    background: #475569;
    color: white;
    transform: translateY(-2px);
}

.action-btn.warning {
    background: #fef3c7;
    color: #d97706;
}

.action-btn.warning:hover {
    background: #d97706;
    color: white;
    transform: translateY(-2px);
}

.action-btn.danger {
    background: #fee2e2;
    color: #dc2626;
}

.action-btn.danger:hover {
    background: #dc2626;
    color: white;
    transform: translateY(-2px);
}

/* Alerts Section */
.alerts-section {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.alert-card {
    background: white;
    border-radius: 20px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.alert-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e2e8f0;
}

.alert-header.warning {
    background: linear-gradient(135deg, #fef3c7, #fffbeb);
}

.alert-header.danger {
    background: linear-gradient(135deg, #fee2e2, #fff5f5);
}

.alert-header i {
    font-size: 1.25rem;
}

.alert-header.warning i { color: #d97706; }
.alert-header.danger i { color: #dc2626; }

.alert-header h3 {
    margin: 0;
    font-size: 1rem;
    font-weight: 600;
    flex: 1;
}

.badge-count {
    background: rgba(0, 0, 0, 0.1);
    padding: 0.25rem 0.5rem;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 600;
}

.alert-body {
    padding: 1rem;
}

.empty-alert {
    text-align: center;
    padding: 2rem;
    color: #94a3b8;
}

.empty-alert i {
    font-size: 2rem;
    margin-bottom: 0.5rem;
    color: #10b981;
}

.empty-alert p {
    margin: 0;
}

.alert-table {
    max-height: 300px;
    overflow-y: auto;
}

.medicine-cell {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.medicine-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.medicine-icon.warning-bg {
    background: linear-gradient(135deg, #f59e0b, #d97706);
}

.medicine-icon.danger-bg {
    background: linear-gradient(135deg, #ef4444, #dc2626);
}

.medicine-name {
    font-weight: 600;
    color: #0f172a;
    margin-bottom: 0.25rem;
}

.medicine-meta {
    font-size: 0.7rem;
    color: #94a3b8;
}

.medicine-dose {
    font-size: 0.65rem;
    color: #2563eb;
    margin-top: 0.125rem;
}

.stock-badge.critical {
    background: #fee2e2;
    color: #dc2626;
    padding: 0.25rem 0.5rem;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.75rem;
    display: inline-block;
}

.expiry-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 600;
    display: inline-block;
}

.expiry-badge.critical {
    background: #fee2e2;
    color: #dc2626;
}

.expiry-badge.warning {
    background: #fef3c7;
    color: #d97706;
}

.expiry-badge.good {
    background: #dcfce7;
    color: #10b981;
}

.alert-footer {
    padding: 1rem;
    border-top: 1px solid #e2e8f0;
    text-align: center;
}

.view-all-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 40px;
    text-decoration: none;
    font-size: 0.75rem;
    font-weight: 600;
    transition: all 0.2s;
}

.view-all-btn.warning {
    background: #fef3c7;
    color: #d97706;
}

.view-all-btn.warning:hover {
    background: #d97706;
    color: white;
}

.view-all-btn.danger {
    background: #fee2e2;
    color: #dc2626;
}

.view-all-btn.danger:hover {
    background: #dc2626;
    color: white;
}

.view-all-btn.primary {
    background: #eff6ff;
    color: #2563eb;
}

.view-all-btn.primary:hover {
    background: #2563eb;
    color: white;
}

/* Sales Section */
.sales-section {
    background: white;
    border-radius: 20px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.sales-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.25rem 1.5rem;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
}

.sales-header .header-left {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.sales-header .header-left i {
    font-size: 1.25rem;
    color: #2563eb;
}

.sales-header .header-left h3 {
    margin: 0;
    font-size: 1rem;
    font-weight: 600;
}

.sales-table-container {
    overflow-x: auto;
    padding: 1rem;
}

/* Modern Table */
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
    font-size: 0.7rem;
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

.invoice-number {
    font-weight: 600;
    color: #2563eb;
}

.datetime-cell span {
    display: block;
    font-weight: 500;
}

.datetime-cell small {
    font-size: 0.65rem;
    color: #94a3b8;
}

.cashier-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.cashier-avatar {
    width: 28px;
    height: 28px;
    background: #2563eb;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.7rem;
    font-weight: 600;
}

.total-cell {
    font-weight: 700;
    color: #10b981;
}

.payment-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.5rem;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 600;
}

.payment-cash {
    background: #dcfce7;
    color: #15803d;
}

.payment-card {
    background: #dbeafe;
    color: #1e40af;
}

.payment-other {
    background: #e0e7ff;
    color: #4338ca;
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
}

.action-icon {
    width: 28px;
    height: 28px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: all 0.2s;
    border: 1px solid #e2e8f0;
    background: white;
    color: #64748b;
}

.action-icon:hover {
    transform: translateY(-2px);
}

.action-icon.view:hover {
    border-color: #2563eb;
    color: #2563eb;
}

.action-icon.edit:hover {
    border-color: #f59e0b;
    color: #f59e0b;
}

.action-icon.print:hover {
    border-color: #10b981;
    color: #10b981;
}

.action-icon.stock:hover {
    border-color: #10b981;
    color: #10b981;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 3rem;
    color: #94a3b8;
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.empty-state h4 {
    margin: 0 0 0.5rem 0;
    font-size: 1rem;
    color: #0f172a;
}

.empty-state p {
    margin: 0 0 1rem 0;
    font-size: 0.875rem;
}

.btn-modern {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 40px;
    font-weight: 600;
    font-size: 0.75rem;
    text-decoration: none;
    transition: all 0.2s;
    border: none;
    cursor: pointer;
}

.btn-primary {
    background: #2563eb;
    color: white;
}

.btn-primary:hover {
    background: #1d4ed8;
    transform: translateY(-1px);
}

/* Responsive */
@media (max-width: 1024px) {
    .dashboard-modern {
        padding: 1rem;
    }
    
    .two-column-grid {
        grid-template-columns: 1fr;
    }
    
    .alerts-section {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .dash-hero-content {
        flex-direction: column;
        gap: 1rem;
    }
    
    .kpi-grid-dash {
        grid-template-columns: 1fr;
    }
    
    .actions-grid {
        grid-template-columns: 1fr;
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sales Chart
    const ctx = document.getElementById('salesChart').getContext('2d');
    
    const formatCurrency = (value) => {
        return '₱' + value.toLocaleString('en-PH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    };
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartData['months']) !!},
            datasets: [{
                label: 'Monthly Sales',
                data: {!! json_encode($chartData['sales']) !!},
                backgroundColor: 'rgba(37, 99, 235, 0.05)',
                borderColor: '#2563eb',
                borderWidth: 2,
                pointBackgroundColor: '#2563eb',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointHoverBackgroundColor: '#2563eb',
                pointHoverBorderColor: '#fff',
                pointHoverBorderWidth: 2,
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: '#0f172a',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    padding: 10,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            return ' Sales: ' + formatCurrency(context.parsed.y);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)',
                        drawBorder: false
                    },
                    ticks: {
                        callback: function(value) {
                            return '₱' + value.toLocaleString('en-PH');
                        },
                        font: { size: 10 },
                        maxTicksLimit: 6
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: {
                        font: { size: 11, weight: '500' },
                        maxRotation: 0,
                        minRotation: 0
                    }
                }
            },
            interaction: {
                mode: 'index',
                intersect: false
            }
        }
    });
});

// Auto-refresh dashboard stats every 60 seconds
function refreshStats() {
    fetch('{{ route("dashboard.stats") }}')
        .then(response => response.json())
        .then(data => {
            document.getElementById('totalMedicines').textContent = data.total_medicines;
            document.getElementById('lowStock').textContent = data.low_stock;
            document.getElementById('todaySales').textContent = '₱' + parseFloat(data.today_sales).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            document.getElementById('monthSales').textContent = '₱' + parseFloat(data.month_sales).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        })
        .catch(error => console.error('Error refreshing stats:', error));
}

setInterval(refreshStats, 60000);
</script>
@endpush
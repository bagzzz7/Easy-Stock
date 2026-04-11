@extends('layouts.app')

@section('title', 'Inventory Management')

@section('content')
<div class="inventory-modern">
    {{-- Modern Header --}}
    <div class="inv-hero">
        <div class="inv-hero-content">
            <div class="inv-hero-left">
                <div class="inv-badge">Inventory Overview</div>
                <h1 class="inv-title">Stock Management</h1>
                <p class="inv-subtitle">Real-time inventory tracking and stock movement analytics</p>
            </div>
            <div class="inv-hero-right">
                <div class="inv-stats-preview">
                    <div class="stat-chip">
                        <i class="fas fa-chart-line"></i>
                        <span>Updated {{ now()->format('H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="inv-actions">
            <button class="btn-modern btn-primary" onclick="openTab('stockIn')">
                <i class="fas fa-plus-circle"></i>
                <span>Stock In</span>
            </button>
            <button class="btn-modern btn-secondary" onclick="openTab('stockOut')">
                <i class="fas fa-minus-circle"></i>
                <span>Stock Out</span>
            </button>
            <button class="btn-modern btn-outline" onclick="openTab('history')">
                <i class="fas fa-history"></i>
                <span>History</span>
            </button>
        </div>
    </div>

    {{-- Modern KPI Cards --}}
    <div class="kpi-grid">
        <div class="kpi-card" data-type="total">
            <div class="kpi-icon blue">
                <i class="fas fa-pills"></i>
            </div>
            <div class="kpi-info">
                <h3>{{ $totalMedicines }}</h3>
                <p>Total Medicines</p>
                <span class="trend">Active medicines</span>
            </div>
        </div>

        <div class="kpi-card" data-type="instock">
            <div class="kpi-icon green">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="kpi-info">
                <h3>{{ $inStockCount }}</h3>
                <p>In Stock</p>
                <span class="trend positive">{{ $totalMedicines ? round($inStockCount / $totalMedicines * 100) : 0 }}% of total</span>
            </div>
        </div>

        <div class="kpi-card" data-type="lowstock">
            <div class="kpi-icon warning">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="kpi-info">
                <h3>{{ $lowStockCount }}</h3>
                <p>Low Stock</p>
                <span class="trend danger">Needs attention</span>
            </div>
        </div>

        <div class="kpi-card" data-type="outofstock">
            <div class="kpi-icon danger">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="kpi-info">
                <h3>{{ $outOfStockCount }}</h3>
                <p>Out of Stock</p>
                <span class="trend">Critical</span>
            </div>
        </div>

        <div class="kpi-card" data-type="expired">
            <div class="kpi-icon purple">
                <i class="fas fa-ban"></i>
            </div>
            <div class="kpi-info">
                <h3>{{ $expiredCount }}</h3>
                <p>Expired</p>
                <span class="trend danger">Requires disposal</span>
            </div>
        </div>

        <div class="kpi-card" data-type="value">
            <div class="kpi-icon teal">
                <i class="fas fa-coins"></i>
            </div>
            <div class="kpi-info">
                <h3>₱{{ number_format($totalStockValue, 0) }}</h3>
                <p>Stock Value</p>
                <span class="trend">Total worth</span>
            </div>
        </div>
    </div>

    {{-- Alerts --}}
    @if($lowStockCount > 0 || $expiredCount > 0)
    <div class="alert-container">
        @if($expiredCount > 0)
        <div class="alert-modern alert-danger">
            <i class="fas fa-skull-crosswalk"></i>
            <div class="alert-content">
                <strong>{{ $expiredCount }} expired medicine(s)</strong>
                <p>Require immediate disposal action</p>
            </div>
            <button class="alert-close">&times;</button>
        </div>
        @endif
        @if($lowStockCount > 0)
        <div class="alert-modern alert-warning">
            <i class="fas fa-truck-fast"></i>
            <div class="alert-content">
                <strong>{{ $lowStockCount }} low stock item(s)</strong>
                <p>Below reorder level, needs replenishment</p>
            </div>
            <button class="alert-close">&times;</button>
        </div>
        @endif
    </div>
    @endif

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="toast-notification success">
        <i class="fas fa-check-circle"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif
    @if(session('error'))
    <div class="toast-notification error">
        <i class="fas fa-exclamation-circle"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    {{-- Modern Tab Navigation --}}
    <div class="tab-navigation">
        <button class="tab-btn active" data-tab="overview">
            <i class="fas fa-table-list"></i>
            <span>Stock Overview</span>
        </button>
        <button class="tab-btn" data-tab="stockIn">
            <i class="fas fa-arrow-trend-down"></i>
            <span>Stock In</span>
        </button>
        <button class="tab-btn" data-tab="stockOut">
            <i class="fas fa-arrow-trend-up"></i>
            <span>Stock Out</span>
        </button>
        <button class="tab-btn" data-tab="history">
            <i class="fas fa-clock-rotate-left"></i>
            <span>Transaction History</span>
            @if($totalMovements > 0)
            <span class="tab-badge">{{ $totalMovements }}</span>
            @endif
        </button>
    </div>

    {{-- Tab Content --}}
    <div class="tab-content">
        {{-- Overview Tab --}}
        <div class="tab-pane active" id="tab-overview">
            <div class="toolbar">
                <div class="search-wrapper">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" placeholder="Search medicine, brand, or batch..." class="search-input">
                </div>
                <div class="filter-group">
                    <select id="categoryFilter" class="filter-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    <select id="statusFilter" class="filter-select">
                        <option value="">All Status</option>
                        <option value="in_stock">In Stock</option>
                        <option value="low_stock">Low Stock</option>
                        <option value="out_of_stock">Out of Stock</option>
                        <option value="expired">Expired</option>
                    </select>
                    <select id="sortFilter" class="filter-select">
                        <option value="name">Sort by Name</option>
                        <option value="quantity">Sort by Stock</option>
                        <option value="expiry">Sort by Expiry</option>
                        <option value="value">Sort by Value</option>
                    </select>
                    <button id="applyFilters" class="btn-filter">
                        <i class="fas fa-sliders-h"></i> Apply
                    </button>
                </div>
                <div class="view-toggle">
                    <button class="view-btn active" data-view="table">
                        <i class="fas fa-table"></i>
                    </button>
                    <button class="view-btn" data-view="grid">
                        <i class="fas fa-th-large"></i>
                    </button>
                </div>
            </div>

            {{-- Table View --}}
            <div id="tableView" class="data-table-container">
                @if($medicines->isEmpty())
                <div class="empty-state">
                    <i class="fas fa-box-open"></i>
                    <h4>No medicines found</h4>
                    <p>Try adjusting your filters or add new medicines</p>
                </div>
                @else
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Medicine</th>
                            <th>Category</th>
                            <th>Stock</th>
                            <th>Price</th>
                            <th>Value</th>
                            <th>Expiry</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($medicines as $medicine)
                        @php
                            $isExpired = $medicine->expiry_date->isPast();
                            $daysLeft = (int) now()->diffInDays($medicine->expiry_date, false);
                            $stockVal = $medicine->quantity * $medicine->purchase_price;
                        @endphp
                        <tr class="product-row {{ $isExpired ? 'expired-row' : '' }}">
                            <td>
                                <div class="product-info">
                                    <div class="product-avatar">
                                        <i class="fas fa-capsules"></i>
                                    </div>
                                    <div>
                                        <div class="product-name">{{ $medicine->name }}</div>
                                        <div class="product-meta">{{ $medicine->brand ?? $medicine->generic_name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="category-badge">{{ $medicine->category->name ?? 'Uncategorized' }}</span></td>
                            <td>
                                <div class="stock-indicator">
                                    <span class="stock-number {{ $medicine->quantity == 0 ? 'critical' : ($medicine->status === 'low_stock' ? 'warning' : 'normal') }}">
                                        {{ number_format($medicine->quantity) }}
                                    </span>
                                    <div class="stock-bar">
                                        <div class="stock-fill" style="width: {{ $medicine->reorder_level > 0 ? min(100, ($medicine->quantity / ($medicine->reorder_level * 3)) * 100) : 100 }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="price-info">
                                    <div class="purchase">₱{{ number_format($medicine->purchase_price, 2) }}</div>
                                    <div class="selling">₱{{ number_format($medicine->selling_price, 2) }}</div>
                                </div>
                            </td>
                            <td class="value-cell">₱{{ number_format($stockVal, 2) }}</td>
                            <td>
                                <div class="expiry-info {{ $isExpired ? 'expired' : ($daysLeft <= 30 ? 'expiring' : '') }}">
                                    {{ $medicine->expiry_date->format('M d, Y') }}
                                    @if($isExpired)
                                    <span class="expiry-badge">Expired</span>
                                    @elseif($daysLeft <= 30)
                                    <span class="expiry-badge warning">{{ $daysLeft }}d left</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="status-badge status-{{ $medicine->status }}">
                                    {{ str_replace('_', ' ', ucfirst($medicine->status)) }}
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="action-icon" onclick="quickStockIn({{ $medicine->id }},'{{ addslashes($medicine->name) }}',{{ $medicine->quantity }},'{{ $medicine->unit }}','{{ $medicine->batch_number }}',{{ $medicine->purchase_price }})" title="Stock In">
                                        <i class="fas fa-arrow-down"></i>
                                    </button>
                                    <button class="action-icon" onclick="quickStockOut({{ $medicine->id }},'{{ addslashes($medicine->name) }}',{{ $medicine->quantity }},'{{ $medicine->unit }}')" title="Stock Out">
                                        <i class="fas fa-arrow-up"></i>
                                    </button>
                                    <a href="{{ route('medicines.show', $medicine) }}" class="action-icon" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="pagination-modern">
                    {{ $medicines->withQueryString()->links() }}
                </div>
                @endif
            </div>

            {{-- Grid View --}}
            <div id="gridView" class="grid-container" style="display: none;">
                @foreach($medicines as $medicine)
                <div class="product-card">
                    <div class="card-header">
                        <span class="status-badge status-{{ $medicine->status }}">{{ str_replace('_', ' ', ucfirst($medicine->status)) }}</span>
                        @if($medicine->requires_prescription)
                        <span class="rx-badge">Rx</span>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="product-icon">
                            <i class="fas fa-tablets"></i>
                        </div>
                        <h4>{{ $medicine->name }}</h4>
                        <p class="brand">{{ $medicine->brand ?? $medicine->generic_name }}</p>
                        <div class="card-stats">
                            <div class="stat">
                                <span>Stock</span>
                                <strong>{{ number_format($medicine->quantity) }}</strong>
                            </div>
                            <div class="stat">
                                <span>Price</span>
                                <strong>₱{{ number_format($medicine->selling_price, 2) }}</strong>
                            </div>
                            <div class="stat">
                                <span>Expiry</span>
                                <strong>{{ $medicine->expiry_date->format('M Y') }}</strong>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="btn-sm btn-in" onclick="quickStockIn({{ $medicine->id }},'{{ addslashes($medicine->name) }}',{{ $medicine->quantity }},'{{ $medicine->unit }}','{{ $medicine->batch_number }}',{{ $medicine->purchase_price }})">
                            <i class="fas fa-arrow-down"></i> Stock In
                        </button>
                        <button class="btn-sm btn-out" onclick="quickStockOut({{ $medicine->id }},'{{ addslashes($medicine->name) }}',{{ $medicine->quantity }},'{{ $medicine->unit }}')">
                            <i class="fas fa-arrow-up"></i> Stock Out
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Stock In Tab --}}
        <div class="tab-pane" id="tab-stockIn">
            <div class="form-modern">
                <div class="form-header">
                    <i class="fas fa-arrow-down"></i>
                    <h3>Stock In Entry</h3>
                    <p>Record new stock received</p>
                </div>
                <form action="{{ route('stock.in.store') }}" method="POST" class="modern-form">
                    @csrf
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Medicine *</label>
                            <select name="medicine_id" id="siMed" class="modern-select" required>
                                <option value="">Select medicine...</option>
                                @foreach($medicines_all as $med)
                                <option value="{{ $med->id }}" data-qty="{{ $med->quantity }}" data-unit="{{ $med->unit }}" data-price="{{ $med->purchase_price }}" data-batch="{{ $med->batch_number }}">
                                    {{ $med->name }} {{ $med->brand ? '- '.$med->brand : '' }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Quantity *</label>
                            <input type="number" name="quantity" id="siQty" class="modern-input" placeholder="Enter quantity" required>
                        </div>
                        <div class="form-group">
                            <label>Unit Cost (₱)</label>
                            <input type="number" name="unit_cost" id="siCost" class="modern-input" step="0.01" placeholder="Purchase price">
                        </div>
                        <div class="form-group">
                            <label>Batch Number</label>
                            <input type="text" name="batch_number" id="siBatch" class="modern-input" placeholder="Batch/Lot number">
                        </div>
                        <div class="form-group">
                            <label>Supplier</label>
                            <select name="supplier_id" class="modern-select">
                                <option value="">Select supplier...</option>
                                @foreach($suppliers as $sup)
                                <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Expiry Date</label>
                            <input type="date" name="expiry_date" class="modern-input" min="{{ now()->addDay()->toDateString() }}">
                        </div>
                        <div class="form-group full-width">
                            <label>Notes</label>
                            <textarea name="notes" class="modern-textarea" rows="3" placeholder="Additional notes..."></textarea>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn-cancel" onclick="switchTab('overview')">Cancel</button>
                        <button type="submit" class="btn-submit btn-in">Confirm Stock In</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Stock Out Tab --}}
        <div class="tab-pane" id="tab-stockOut">
            <div class="form-modern">
                <div class="form-header">
                    <i class="fas fa-arrow-up"></i>
                    <h3>Stock Out Entry</h3>
                    <p>Record stock removal or sales</p>
                </div>
                <form action="{{ route('stock.out.store') }}" method="POST" class="modern-form">
                    @csrf
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Medicine *</label>
                            <select name="medicine_id" id="soMed" class="modern-select" required>
                                <option value="">Select medicine...</option>
                                @foreach($medicines_all as $med)
                                <option value="{{ $med->id }}" data-qty="{{ $med->quantity }}" data-unit="{{ $med->unit }}">
                                    {{ $med->name }} ({{ $med->quantity }} {{ $med->unit }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Quantity *</label>
                            <input type="number" name="quantity" id="soQty" class="modern-input" placeholder="Enter quantity" required>
                        </div>
                        <div class="form-group full-width">
                            <label>Reason *</label>
                            <div class="reason-grid">
                                <label class="reason-option">
                                    <input type="radio" name="reason" value="sale" checked>
                                    <i class="fas fa-shopping-cart"></i>
                                    <span>Sale</span>
                                </label>
                                <label class="reason-option">
                                    <input type="radio" name="reason" value="expired">
                                    <i class="fas fa-ban"></i>
                                    <span>Expired</span>
                                </label>
                                <label class="reason-option">
                                    <input type="radio" name="reason" value="damaged">
                                    <i class="fas fa-box-open"></i>
                                    <span>Damaged</span>
                                </label>
                                <label class="reason-option">
                                    <input type="radio" name="reason" value="transfer">
                                    <i class="fas fa-exchange-alt"></i>
                                    <span>Transfer</span>
                                </label>
                                <label class="reason-option">
                                    <input type="radio" name="reason" value="adjustment">
                                    <i class="fas fa-sliders-h"></i>
                                    <span>Adjustment</span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group full-width">
                            <label>Notes</label>
                            <textarea name="notes" class="modern-textarea" rows="3" placeholder="Additional notes..."></textarea>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn-cancel" onclick="switchTab('overview')">Cancel</button>
                        <button type="submit" class="btn-submit btn-out">Confirm Stock Out</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- History Tab --}}
        <div class="tab-pane" id="tab-history">
            <div class="history-filters">
                <div class="filter-row">
                    <select name="hist_type" id="histType" class="filter-select">
                        <option value="">All Types</option>
                        <option value="stock_in">Stock In</option>
                        <option value="stock_out">Stock Out</option>
                    </select>
                    <select name="hist_medicine" id="histMedicine" class="filter-select">
                        <option value="">All Medicines</option>
                        @foreach($medicines_all as $m)
                        <option value="{{ $m->id }}">{{ $m->name }}</option>
                        @endforeach
                    </select>
                    <select name="hist_reason" id="histReason" class="filter-select">
                        <option value="">All Reasons</option>
                        <option value="purchase">Purchase</option>
                        <option value="sale">Sale</option>
                        <option value="expired">Expired</option>
                        <option value="damaged">Damaged</option>
                        <option value="transfer">Transfer</option>
                        <option value="adjustment">Adjustment</option>
                    </select>
                    <input type="date" id="histFrom" class="filter-date" placeholder="From">
                    <input type="date" id="histTo" class="filter-date" placeholder="To">
                    <button id="applyHistoryFilters" class="btn-filter">Apply Filters</button>
                </div>
            </div>

            @if($movements->isEmpty())
            <div class="empty-state">
                <i class="fas fa-history"></i>
                <h4>No transactions yet</h4>
                <p>Use Stock In/Out tabs to record movements</p>
            </div>
            @else
            <div class="data-table-container">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Date & Time</th>
                            <th>Medicine</th>
                            <th>Type</th>
                            <th>Reason</th>
                            <th>Quantity Change</th>
                            <th>Stock (Before → After)</th>
                            <th>User</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($movements as $mov)
                        <tr>
                            <td>
                                <div class="datetime-cell">
                                    <span>{{ $mov->created_at->format('M d, Y') }}</span>
                                    <small>{{ $mov->created_at->format('h:i A') }}</small>
                                </div>
                            </td>
                            <td>
                                <strong>{{ $mov->medicine->name ?? 'N/A' }}</strong>
                            </td>
                            <td>
                                <span class="type-badge type-{{ $mov->type }}">
                                    {{ $mov->type === 'stock_in' ? 'Stock In' : 'Stock Out' }}
                                </span>
                            </td>
                            <td>
                                <span class="reason-badge">{{ $mov->reason_label }}</span>
                            </td>
                            <td>
                                <span class="quantity-change {{ $mov->type === 'stock_in' ? 'positive' : 'negative' }}">
                                    {{ $mov->type === 'stock_in' ? '+' : '-' }}{{ number_format($mov->quantity) }}
                                </span>
                            </td>
                            <td>
                                <span class="stock-change">{{ number_format($mov->quantity_before) }} → {{ number_format($mov->quantity_after) }}</span>
                            </td>
                            <td>
                                <div class="history-user-info">
                                    <div class="history-user-avatar">{{ strtoupper(substr($mov->user->name ?? '?', 0, 1)) }}</div>
                                    <span class="history-user-name">{{ $mov->user->name ?? 'System' }}</span>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="pagination-modern">
                    {{ $movements->withQueryString()->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Quick Stock In Modal --}}
<div class="modal fade" id="quickInModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background:#dcfce7;border-bottom:1px solid #86efac">
                <h5 class="modal-title text-success"><i class="fas fa-arrow-down me-2"></i>Stock In — <span id="qiName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('stock.in.store') }}" method="POST">
                @csrf
                <input type="hidden" name="medicine_id" id="qiMedId">
                <div class="modal-body">
                    <div class="stk-preview mb-3" style="display:flex">
                        <div class="stk-preview__item"><span>Current</span><strong id="qiCurrent">—</strong></div>
                        <div class="stk-preview__item"><span>After</span><strong id="qiAfter" class="text-success">—</strong></div>
                    </div>
                    <div class="mb-3"><label class="stk-label">Quantity *</label><input type="number" name="quantity" id="qiQty" class="stk-input" min="1" placeholder="e.g. 50" required></div>
                    <div class="mb-3"><label class="stk-label">Notes</label><textarea name="notes" class="stk-textarea" rows="2" placeholder="Optional…"></textarea></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="inv-btn inv-btn--ghost" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="inv-btn inv-btn--in"><i class="fas fa-check"></i> Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Quick Stock Out Modal --}}
<div class="modal fade" id="quickOutModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background:#fee2e2;border-bottom:1px solid #fca5a5">
                <h5 class="modal-title text-danger"><i class="fas fa-arrow-up me-2"></i>Stock Out — <span id="qoName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('stock.out.store') }}" method="POST">
                @csrf
                <input type="hidden" name="medicine_id" id="qoMedId">
                <div class="modal-body">
                    <div class="stk-preview mb-3" style="display:flex">
                        <div class="stk-preview__item"><span>Available</span><strong id="qoAvail">—</strong></div>
                        <div class="stk-preview__item"><span>Remaining</span><strong id="qoRemain">—</strong></div>
                    </div>
                    <div class="mb-3"><label class="stk-label">Quantity *</label><input type="number" name="quantity" id="qoQty" class="stk-input" min="1" placeholder="e.g. 10" required></div>
                    <div class="mb-3"><label class="stk-label">Reason *</label><select name="reason" class="stk-select" required><option value="sale">Sale</option><option value="expired">Expired</option><option value="damaged">Damaged/Lost</option><option value="transfer">Transfer Out</option><option value="adjustment">Adjustment</option></select></div>
                    <div class="mb-3"><label class="stk-label">Notes</label><textarea name="notes" class="stk-textarea" rows="2" placeholder="Optional…"></textarea></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="inv-btn inv-btn--ghost" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="inv-btn inv-btn--out"><i class="fas fa-check"></i> Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
/* Modern CSS Variables - Scoped to inventory page */
.inventory-modern {
    --primary: #2563eb;
    --primary-dark: #1d4ed8;
    --secondary: #64748b;
    --success: #10b981;
    --warning: #f59e0b;
    --danger: #ef4444;
    --info: #3b82f6;
    --dark: #0f172a;
    --light: #f8fafc;
    --gray-50: #f9fafb;
    --gray-100: #f1f5f9;
    --gray-200: #e2e8f0;
    --gray-300: #cbd5e1;
    --gray-400: #94a3b8;
    --gray-500: #64748b;
    --gray-600: #475569;
    --gray-700: #334155;
    --gray-800: #1e293b;
    --radius: 12px;
    --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
    --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
    max-width: 1600px;
    margin: 0 auto;
    padding: 2rem;
}

/* Hero Section */
.inventory-modern .inv-hero {
    background: linear-gradient(135deg, var(--dark) 0%, var(--gray-800) 100%);
    border-radius: 24px;
    padding: 2rem;
    margin-bottom: 2rem;
    color: white;
    position: relative;
    overflow: hidden;
}

.inventory-modern .inv-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(37, 99, 235, 0.1) 0%, transparent 70%);
    pointer-events: none;
}

.inventory-modern .inv-hero-content {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 2rem;
    position: relative;
    z-index: 1;
}

.inventory-modern .inv-badge {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 500;
    margin-bottom: 1rem;
}

.inventory-modern .inv-title {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.inventory-modern .inv-subtitle {
    color: var(--gray-300);
    margin: 0;
}

.inventory-modern .stat-chip {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    padding: 0.5rem 1rem;
    border-radius: 40px;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
}

.inventory-modern .inv-actions {
    display: flex;
    gap: 1rem;
    position: relative;
    z-index: 1;
}

.inventory-modern .btn-modern {
    padding: 0.75rem 1.5rem;
    border-radius: 40px;
    font-weight: 600;
    font-size: 0.875rem;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
}

.inventory-modern .btn-primary {
    background: var(--primary);
    color: white;
}

.inventory-modern .btn-primary:hover {
    background: var(--primary-dark);
    transform: translateY(-2px);
}

.inventory-modern .btn-secondary {
    background: var(--success);
    color: white;
}

.inventory-modern .btn-secondary:hover {
    background: #0d9488;
    transform: translateY(-2px);
}

.inventory-modern .btn-outline {
    background: transparent;
    border: 1px solid rgba(255, 255, 255, 0.3);
    color: white;
}

.inventory-modern .btn-outline:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: white;
}

/* KPI Grid */
.inventory-modern .kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.inventory-modern .kpi-card {
    background: white;
    border-radius: var(--radius);
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: var(--shadow);
    transition: all 0.3s;
    border: 1px solid var(--gray-200);
}

.inventory-modern .kpi-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
}

.inventory-modern .kpi-icon {
    width: 56px;
    height: 56px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.inventory-modern .kpi-icon.blue { background: linear-gradient(135deg, #3b82f6, #1e40af); color: white; }
.inventory-modern .kpi-icon.green { background: linear-gradient(135deg, #10b981, #047857); color: white; }
.inventory-modern .kpi-icon.warning { background: linear-gradient(135deg, #f59e0b, #d97706); color: white; }
.inventory-modern .kpi-icon.danger { background: linear-gradient(135deg, #ef4444, #dc2626); color: white; }
.inventory-modern .kpi-icon.purple { background: linear-gradient(135deg, #8b5cf6, #6d28d9); color: white; }
.inventory-modern .kpi-icon.teal { background: linear-gradient(135deg, #14b8a6, #0f766e); color: white; }

.inventory-modern .kpi-info h3 {
    font-size: 1.75rem;
    font-weight: 700;
    margin: 0;
    color: var(--dark);
}

.inventory-modern .kpi-info p {
    margin: 0;
    font-size: 0.875rem;
    color: var(--gray-500);
    font-weight: 500;
}

.inventory-modern .trend {
    font-size: 0.75rem;
    color: var(--gray-400);
}

.inventory-modern .trend.positive { color: var(--success); }
.inventory-modern .trend.danger { color: var(--danger); }

/* Alerts */
.inventory-modern .alert-container {
    margin-bottom: 2rem;
}

.inventory-modern .alert-modern {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border-radius: var(--radius);
    margin-bottom: 0.75rem;
    animation: slideIn 0.3s ease;
}

.inventory-modern .alert-danger {
    background: linear-gradient(135deg, #fee2e2, #fff5f5);
    border-left: 4px solid var(--danger);
}

.inventory-modern .alert-warning {
    background: linear-gradient(135deg, #fef3c7, #fffbeb);
    border-left: 4px solid var(--warning);
}

.inventory-modern .alert-modern i:first-child {
    font-size: 1.5rem;
}

.inventory-modern .alert-modern .alert-content {
    flex: 1;
}

.inventory-modern .alert-modern strong {
    display: block;
    margin-bottom: 0.25rem;
}

.inventory-modern .alert-modern p {
    margin: 0;
    font-size: 0.875rem;
    color: var(--gray-600);
}

.inventory-modern .alert-close {
    background: none;
    border: none;
    font-size: 1.25rem;
    cursor: pointer;
    opacity: 0.5;
    transition: opacity 0.2s;
}

.inventory-modern .alert-close:hover {
    opacity: 1;
}

/* Toast Notifications */
.inventory-modern .toast-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 1rem 1.5rem;
    border-radius: var(--radius);
    display: flex;
    align-items: center;
    gap: 0.75rem;
    z-index: 1000;
    animation: slideInRight 0.3s ease;
    box-shadow: var(--shadow-lg);
}

.inventory-modern .toast-notification.success {
    background: var(--success);
    color: white;
}

.inventory-modern .toast-notification.error {
    background: var(--danger);
    color: white;
}

/* Tab Navigation */
.inventory-modern .tab-navigation {
    display: flex;
    gap: 0.5rem;
    background: white;
    padding: 0.5rem;
    border-radius: var(--radius);
    margin-bottom: 2rem;
    box-shadow: var(--shadow);
    border: 1px solid var(--gray-200);
}

.inventory-modern .tab-btn {
    flex: 1;
    padding: 0.75rem 1.5rem;
    background: transparent;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    cursor: pointer;
    transition: all 0.2s;
    color: var(--gray-600);
}

.inventory-modern .tab-btn i {
    font-size: 1rem;
}

.inventory-modern .tab-btn.active {
    background: var(--primary);
    color: white;
}

.inventory-modern .tab-btn:not(.active):hover {
    background: var(--gray-100);
    color: var(--dark);
}

.inventory-modern .tab-badge {
    background: var(--danger);
    color: white;
    font-size: 0.7rem;
    padding: 0.125rem 0.5rem;
    border-radius: 20px;
    margin-left: 0.5rem;
}

.inventory-modern .tab-content .tab-pane {
    display: none;
}

.inventory-modern .tab-content .tab-pane.active {
    display: block;
    animation: fadeIn 0.3s ease;
}

/* Toolbar */
.inventory-modern .toolbar {
    background: white;
    border-radius: var(--radius);
    padding: 1rem;
    margin-bottom: 1.5rem;
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    align-items: center;
    justify-content: space-between;
    border: 1px solid var(--gray-200);
}

.inventory-modern .search-wrapper {
    flex: 1;
    min-width: 250px;
    position: relative;
}

.inventory-modern .search-wrapper i {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--gray-400);
}

.inventory-modern .search-input {
    width: 100%;
    padding: 0.625rem 1rem 0.625rem 2.5rem;
    border: 1px solid var(--gray-200);
    border-radius: 40px;
    font-size: 0.875rem;
    transition: all 0.2s;
}

.inventory-modern .search-input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.inventory-modern .filter-group {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.inventory-modern .filter-select,
.inventory-modern .filter-date {
    padding: 0.625rem 1rem;
    border: 1px solid var(--gray-200);
    border-radius: 40px;
    font-size: 0.875rem;
    background: white;
    cursor: pointer;
    transition: all 0.2s;
}

.inventory-modern .filter-select:focus,
.inventory-modern .filter-date:focus {
    outline: none;
    border-color: var(--primary);
}

.inventory-modern .btn-filter {
    padding: 0.625rem 1.25rem;
    background: var(--primary);
    color: white;
    border: none;
    border-radius: 40px;
    font-weight: 600;
    font-size: 0.875rem;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.inventory-modern .btn-filter:hover {
    background: var(--primary-dark);
    transform: translateY(-1px);
}

.inventory-modern .view-toggle {
    display: flex;
    gap: 0.25rem;
    background: var(--gray-100);
    padding: 0.25rem;
    border-radius: 40px;
}

.inventory-modern .view-btn {
    padding: 0.5rem;
    border: none;
    background: transparent;
    border-radius: 40px;
    cursor: pointer;
    transition: all 0.2s;
    color: var(--gray-600);
}

.inventory-modern .view-btn.active {
    background: white;
    color: var(--primary);
    box-shadow: var(--shadow-sm);
}

/* Modern Table */
.inventory-modern .modern-table {
    width: 100%;
    background: white;
    border-radius: var(--radius);
    border-collapse: collapse;
    overflow: hidden;
    box-shadow: var(--shadow);
}

.inventory-modern .modern-table thead {
    background: var(--gray-50);
    border-bottom: 1px solid var(--gray-200);
}

.inventory-modern .modern-table th {
    padding: 1rem;
    text-align: left;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--gray-500);
}

.inventory-modern .modern-table td {
    padding: 1rem;
    border-bottom: 1px solid var(--gray-100);
}

.inventory-modern .modern-table tbody tr:hover {
    background: var(--gray-50);
}

.inventory-modern .product-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.inventory-modern .product-avatar {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.inventory-modern .product-name {
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 0.25rem;
}

.inventory-modern .product-meta {
    font-size: 0.75rem;
    color: var(--gray-500);
}

.inventory-modern .category-badge {
    background: var(--gray-100);
    padding: 0.25rem 0.5rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 500;
    color: var(--gray-600);
}

.inventory-modern .stock-indicator {
    min-width: 100px;
}

.inventory-modern .stock-number {
    font-weight: 700;
    display: block;
    margin-bottom: 0.25rem;
}

.inventory-modern .stock-number.critical { color: var(--danger); }
.inventory-modern .stock-number.warning { color: var(--warning); }
.inventory-modern .stock-number.normal { color: var(--success); }

.inventory-modern .stock-bar {
    height: 4px;
    background: var(--gray-200);
    border-radius: 2px;
    overflow: hidden;
}

.inventory-modern .stock-fill {
    height: 100%;
    background: var(--success);
    border-radius: 2px;
    transition: width 0.3s;
}

.inventory-modern .price-info .purchase {
    font-size: 0.75rem;
    color: var(--gray-500);
    text-decoration: line-through;
}

.inventory-modern .price-info .selling {
    font-weight: 600;
    color: var(--dark);
}

.inventory-modern .value-cell {
    font-weight: 600;
    color: var(--success);
}

.inventory-modern .expiry-info {
    font-size: 0.875rem;
}

.inventory-modern .expiry-info.expired {
    color: var(--danger);
}

.inventory-modern .expiry-info.expiring {
    color: var(--warning);
}

.inventory-modern .expiry-badge {
    font-size: 0.7rem;
    padding: 0.125rem 0.375rem;
    background: var(--gray-100);
    border-radius: 4px;
    margin-left: 0.5rem;
}

.inventory-modern .expiry-badge.warning {
    background: var(--warning);
    color: white;
}

.inventory-modern .status-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}

.inventory-modern .status-in_stock { background: #dcfce7; color: #15803d; }
.inventory-modern .status-low_stock { background: #fef3c7; color: #a16207; }
.inventory-modern .status-out_of_stock { background: #f1f5f9; color: #475569; }
.inventory-modern .status-expired { background: #fee2e2; color: #991b1b; }

.inventory-modern .action-buttons {
    display: flex;
    gap: 0.5rem;
}

.inventory-modern .action-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    border: 1px solid var(--gray-200);
    background: white;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
    color: var(--gray-600);
}

.inventory-modern .action-icon:hover {
    border-color: var(--primary);
    color: var(--primary);
    transform: translateY(-2px);
}

/* Grid View */
.inventory-modern .grid-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
}

.inventory-modern .product-card {
    background: white;
    border-radius: var(--radius);
    overflow: hidden;
    transition: all 0.3s;
    box-shadow: var(--shadow);
    border: 1px solid var(--gray-200);
}

.inventory-modern .product-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
}

.inventory-modern .card-header {
    padding: 1rem;
    background: var(--gray-50);
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid var(--gray-200);
}

.inventory-modern .rx-badge {
    background: var(--danger);
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.7rem;
    font-weight: 600;
}

.inventory-modern .card-body {
    padding: 1.5rem;
    text-align: center;
}

.inventory-modern .product-icon {
    width: 64px;
    height: 64px;
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    border-radius: 20px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1rem;
    color: white;
    font-size: 2rem;
}

.inventory-modern .card-body h4 {
    margin: 0 0 0.5rem 0;
    font-size: 1.125rem;
    font-weight: 600;
}

.inventory-modern .card-body .brand {
    color: var(--gray-500);
    font-size: 0.875rem;
    margin-bottom: 1rem;
}

.inventory-modern .card-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    background: var(--gray-50);
    padding: 1rem;
    border-radius: var(--radius);
}

.inventory-modern .card-stats .stat {
    text-align: center;
}

.inventory-modern .card-stats .stat span {
    display: block;
    font-size: 0.7rem;
    color: var(--gray-500);
    margin-bottom: 0.25rem;
}

.inventory-modern .card-stats .stat strong {
    font-size: 1rem;
    color: var(--dark);
}

.inventory-modern .card-footer {
    padding: 1rem;
    display: flex;
    gap: 0.5rem;
    border-top: 1px solid var(--gray-200);
}

.inventory-modern .btn-sm {
    flex: 1;
    padding: 0.5rem;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.75rem;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.inventory-modern .btn-in {
    background: var(--success);
    color: white;
}

.inventory-modern .btn-in:hover {
    background: #0d9488;
}

.inventory-modern .btn-out {
    background: var(--danger);
    color: white;
}

.inventory-modern .btn-out:hover {
    background: #dc2626;
}

/* Form Styles */
.inventory-modern .form-modern {
    background: white;
    border-radius: var(--radius);
    padding: 2rem;
    box-shadow: var(--shadow);
}

.inventory-modern .form-header {
    text-align: center;
    margin-bottom: 2rem;
}

.inventory-modern .form-header i {
    font-size: 2rem;
    color: var(--primary);
    margin-bottom: 1rem;
}

.inventory-modern .form-header h3 {
    margin: 0 0 0.5rem 0;
    font-size: 1.5rem;
}

.inventory-modern .form-header p {
    margin: 0;
    color: var(--gray-500);
}

.inventory-modern .form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
    margin-bottom: 2rem;
}

.inventory-modern .form-group.full-width {
    grid-column: span 2;
}

.inventory-modern .form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    font-size: 0.875rem;
    color: var(--gray-700);
}

.inventory-modern .modern-input,
.inventory-modern .modern-select,
.inventory-modern .modern-textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid var(--gray-200);
    border-radius: 8px;
    font-size: 0.875rem;
    transition: all 0.2s;
}

.inventory-modern .modern-input:focus,
.inventory-modern .modern-select:focus,
.inventory-modern .modern-textarea:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.inventory-modern .modern-textarea {
    resize: vertical;
    font-family: inherit;
}

.inventory-modern .reason-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
    gap: 0.75rem;
}

.inventory-modern .reason-option {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem;
    border: 2px solid var(--gray-200);
    border-radius: var(--radius);
    cursor: pointer;
    transition: all 0.2s;
}

.inventory-modern .reason-option:hover {
    border-color: var(--primary);
    background: var(--gray-50);
}

.inventory-modern .reason-option input {
    display: none;
}

.inventory-modern .reason-option:has(input:checked) {
    border-color: var(--primary);
    background: rgba(37, 99, 235, 0.05);
}

.inventory-modern .form-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
}

.inventory-modern .btn-cancel {
    padding: 0.75rem 1.5rem;
    background: transparent;
    border: 1px solid var(--gray-200);
    border-radius: 40px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.inventory-modern .btn-cancel:hover {
    background: var(--gray-100);
}

.inventory-modern .btn-submit {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 40px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    color: white;
}

.inventory-modern .btn-submit.btn-in {
    background: var(--success);
}

.inventory-modern .btn-submit.btn-out {
    background: var(--danger);
}

.inventory-modern .btn-submit:hover {
    transform: translateY(-1px);
    filter: brightness(1.05);
}

/* History Section */
.inventory-modern .history-filters {
    background: white;
    border-radius: var(--radius);
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: var(--shadow);
}

.inventory-modern .filter-row {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
    align-items: center;
}

.inventory-modern .datetime-cell {
    font-size: 0.875rem;
}

.inventory-modern .datetime-cell small {
    font-size: 0.7rem;
    color: var(--gray-500);
}

.inventory-modern .type-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}

.inventory-modern .type-badge.type-stock_in {
    background: #dcfce7;
    color: #15803d;
}

.inventory-modern .type-badge.type-stock_out {
    background: #fee2e2;
    color: #991b1b;
}

.inventory-modern .reason-badge {
    background: var(--gray-100);
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.7rem;
    font-weight: 500;
    color: var(--gray-600);
}

.inventory-modern .quantity-change {
    font-weight: 700;
}

.inventory-modern .quantity-change.positive {
    color: var(--success);
}

.inventory-modern .quantity-change.negative {
    color: var(--danger);
}

.inventory-modern .stock-change {
    font-size: 0.875rem;
    font-family: monospace;
}

/* History user styles - unique to avoid sidebar conflicts */
.inventory-modern .history-user-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.inventory-modern .history-user-avatar {
    width: 28px;
    height: 28px;
    background: var(--primary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.7rem;
    font-weight: 600;
}

.inventory-modern .history-user-name {
    font-size: 0.875rem;
    color: var(--gray-700);
}

/* Pagination */
.inventory-modern .pagination-modern {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 1.5rem;
    gap: 0.5rem;
}

.inventory-modern .pagination-modern .pagination {
    margin: 0;
    display: flex;
    gap: 0.25rem;
}

.inventory-modern .pagination-modern .page-item .page-link {
    padding: 0.5rem 0.75rem;
    border-radius: 8px;
    border: 1px solid var(--gray-200);
    color: var(--gray-600);
    transition: all 0.2s;
}

.inventory-modern .pagination-modern .page-item.active .page-link {
    background: var(--primary);
    border-color: var(--primary);
    color: white;
}

.inventory-modern .pagination-modern .page-item .page-link:hover:not(.active) {
    background: var(--gray-100);
    border-color: var(--gray-300);
}

/* Empty State */
.inventory-modern .empty-state {
    text-align: center;
    padding: 4rem;
    background: white;
    border-radius: var(--radius);
    box-shadow: var(--shadow);
}

.inventory-modern .empty-state i {
    font-size: 3rem;
    color: var(--gray-300);
    margin-bottom: 1rem;
}

.inventory-modern .empty-state h4 {
    margin: 0 0 0.5rem 0;
    font-size: 1.25rem;
}

.inventory-modern .empty-state p {
    margin: 0;
    color: var(--gray-500);
}

/* Animations */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(100px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

/* Responsive */
@media (max-width: 1024px) {
    .inventory-modern {
        padding: 1rem;
    }
    
    .inventory-modern .kpi-grid {
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    }
    
    .inventory-modern .form-grid {
        grid-template-columns: 1fr;
    }
    
    .inventory-modern .form-group.full-width {
        grid-column: span 1;
    }
}

@media (max-width: 768px) {
    .inventory-modern .inv-hero-content {
        flex-direction: column;
        gap: 1rem;
    }
    
    .inventory-modern .inv-actions {
        flex-wrap: wrap;
    }
    
    .inventory-modern .toolbar {
        flex-direction: column;
        align-items: stretch;
    }
    
    .inventory-modern .filter-group {
        flex-wrap: wrap;
    }
    
    .inventory-modern .modern-table {
        display: block;
        overflow-x: auto;
    }
    
    .inventory-modern .grid-container {
        grid-template-columns: 1fr;
    }
    
    .inventory-modern .reason-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>
@endpush

@push('scripts')
<script>
// Tab switching - only scrolls on explicit user interaction
function switchTab(tabName) {
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelector(`.tab-btn[data-tab="${tabName}"]`).classList.add('active');

    document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('active'));
    document.getElementById(`tab-${tabName}`).classList.add('active');

    localStorage.setItem('invTab', tabName);

    const tabNav = document.querySelector('.tab-navigation');
    if (tabNav) {
        setTimeout(() => {
            tabNav.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 50);
    }
}

function openTab(tabName) {
    switchTab(tabName);
}

// View toggle
let currentView = localStorage.getItem('invView') || 'table';

function setView(view) {
    currentView = view;
    document.getElementById('tableView').style.display = view === 'table' ? 'block' : 'none';
    document.getElementById('gridView').style.display = view === 'grid' ? 'block' : 'none';

    document.querySelectorAll('.view-btn').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.view === view);
    });

    localStorage.setItem('invView', view);
}

document.querySelectorAll('.view-btn').forEach(btn => {
    btn.addEventListener('click', () => setView(btn.dataset.view));
});

// Tab buttons - attach click listeners
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        switchTab(btn.dataset.tab);
    });
});

// Initialize - restore tab state WITHOUT scrolling
document.addEventListener('DOMContentLoaded', () => {
    const savedTab = localStorage.getItem('invTab') || 'overview';

    // Directly restore tab state, bypassing switchTab to avoid scroll on load
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    const activeBtn = document.querySelector(`.tab-btn[data-tab="${savedTab}"]`);
    if (activeBtn) activeBtn.classList.add('active');

    document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('active'));
    const activePane = document.getElementById(`tab-${savedTab}`);
    if (activePane) activePane.classList.add('active');

    setView(currentView);

    // Close alerts
    document.querySelectorAll('.alert-close').forEach(btn => {
        btn.addEventListener('click', () => {
            btn.closest('.alert-modern').remove();
        });
    });

    // Auto-hide toast notifications after 3s
    setTimeout(() => {
        document.querySelectorAll('.toast-notification').forEach(toast => {
            toast.style.animation = 'slideInRight 0.3s reverse';
            setTimeout(() => toast.remove(), 300);
        });
    }, 3000);
});

// Quick Stock In modal
function quickStockIn(id, name, qty, unit, batch, price) {
    document.getElementById('qiMedId').value = id;
    document.getElementById('qiName').textContent = name;
    document.getElementById('qiCurrent').textContent = qty + ' ' + unit;
    document.getElementById('qiAfter').textContent = qty + ' ' + unit;
    const el = document.getElementById('qiQty');
    el.value = '';
    el.oninput = function () {
        document.getElementById('qiAfter').textContent = (qty + (parseInt(this.value) || 0)) + ' ' + unit;
    };
    new bootstrap.Modal(document.getElementById('quickInModal')).show();
}

// Quick Stock Out modal
function quickStockOut(id, name, qty, unit) {
    document.getElementById('qoMedId').value = id;
    document.getElementById('qoName').textContent = name;
    document.getElementById('qoAvail').textContent = qty + ' ' + unit;
    document.getElementById('qoRemain').textContent = qty + ' ' + unit;
    const el = document.getElementById('qoQty');
    el.value = '';
    el.oninput = function () {
        const r = qty - (parseInt(this.value) || 0);
        document.getElementById('qoRemain').textContent = r + ' ' + unit;
        document.getElementById('qoRemain').className = r < 0
            ? 'fw-bold text-danger'
            : (r === 0 ? 'fw-bold text-warning' : 'fw-bold text-success');
    };
    new bootstrap.Modal(document.getElementById('quickOutModal')).show();
}

// Overview filters
document.getElementById('applyFilters')?.addEventListener('click', () => {
    const search   = document.getElementById('searchInput').value;
    const category = document.getElementById('categoryFilter').value;
    const status   = document.getElementById('statusFilter').value;
    const sort     = document.getElementById('sortFilter').value;

    const url = new URL(window.location.href);
    search   ? url.searchParams.set('search', search)           : url.searchParams.delete('search');
    category ? url.searchParams.set('category_id', category)   : url.searchParams.delete('category_id');
    status   ? url.searchParams.set('status', status)           : url.searchParams.delete('status');
    sort     ? url.searchParams.set('sort', sort)               : url.searchParams.delete('sort');

    window.location.href = url.toString();
});

// History filters
document.getElementById('applyHistoryFilters')?.addEventListener('click', () => {
    const type     = document.getElementById('histType').value;
    const medicine = document.getElementById('histMedicine').value;
    const reason   = document.getElementById('histReason').value;
    const from     = document.getElementById('histFrom').value;
    const to       = document.getElementById('histTo').value;

    const url = new URL(window.location.href);
    url.searchParams.set('_tab', 'history');
    type     ? url.searchParams.set('hist_type', type)         : url.searchParams.delete('hist_type');
    medicine ? url.searchParams.set('hist_medicine', medicine) : url.searchParams.delete('hist_medicine');
    reason   ? url.searchParams.set('hist_reason', reason)     : url.searchParams.delete('hist_reason');
    from     ? url.searchParams.set('hist_from', from)         : url.searchParams.delete('hist_from');
    to       ? url.searchParams.set('hist_to', to)             : url.searchParams.delete('hist_to');

    window.location.href = url.toString();
});

// Stock In tab preview
const siMed = document.getElementById('siMed');
const siQty = document.getElementById('siQty');
if (siMed && siQty) {
    function updateSiPreview() {
        const opt = siMed.options[siMed.selectedIndex];
        if (!opt.value) return;
        const cur = parseInt(opt.dataset.qty) || 0;
        const add = parseInt(siQty.value) || 0;
        const siCurrent = document.getElementById('siCurrent');
        const siAfter   = document.getElementById('siAfter');
        if (siCurrent) siCurrent.textContent = cur + ' ' + opt.dataset.unit;
        if (siAfter)   siAfter.textContent   = (cur + add) + ' ' + opt.dataset.unit;
        const batch = document.getElementById('siBatch');
        const cost  = document.getElementById('siCost');
        if (batch && !batch.value) batch.value = opt.dataset.batch || '';
        if (cost  && !cost.value)  cost.value  = opt.dataset.price || '';
    }
    siMed.addEventListener('change', updateSiPreview);
    siQty.addEventListener('input',  updateSiPreview);
}

// Stock Out tab preview
const soMed = document.getElementById('soMed');
const soQty = document.getElementById('soQty');
if (soMed && soQty) {
    function updateSoPreview() {
        const opt    = soMed.options[soMed.selectedIndex];
        if (!opt.value) return;
        const avail  = parseInt(opt.dataset.qty) || 0;
        const deduct = parseInt(soQty.value) || 0;
        const remain = avail - deduct;
        const soAvail  = document.getElementById('soAvail');
        const soDeduct = document.getElementById('soDeduct');
        const soRemain = document.getElementById('soRemain');
        if (soAvail)  soAvail.textContent  = avail  + ' ' + opt.dataset.unit;
        if (soDeduct) soDeduct.textContent = deduct + ' ' + opt.dataset.unit;
        if (soRemain) {
            soRemain.textContent = remain + ' ' + opt.dataset.unit;
            soRemain.className   = remain < 0
                ? 'text-danger fw-bold'
                : (remain === 0 ? 'text-warning fw-bold' : 'text-success fw-bold');
        }
        const warn   = document.getElementById('soWarn');
        const submit = document.getElementById('soSubmit');
        if (warn)   warn.style.display = deduct > avail ? 'flex' : 'none';
        if (submit) submit.disabled    = deduct > avail;
    }
    soMed.addEventListener('change', updateSoPreview);
    soQty.addEventListener('input',  updateSoPreview);
}
</script>
@endpush
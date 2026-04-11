@extends('layouts.app')

@section('title', 'Medicines')

@section('content')
<div class="medicines-modern">
    {{-- Hero Section --}}
    <div class="med-hero">
        <div class="med-hero-content">
            <div class="med-hero-left">
                <div class="med-badge">Inventory Catalog</div>
                <h1 class="med-title">Medicines Management</h1>
                <p class="med-subtitle">Complete pharmaceutical inventory with real-time tracking</p>
            </div>
            <div class="med-hero-right">
                <div class="med-stats-preview">
                    <div class="stat-chip">
                        <i class="fas fa-chart-line"></i>
                        <span>{{ $medicines->total() }} total medicines</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="med-actions">
            <a href="{{ route('medicines.create') }}" class="btn-modern btn-primary">
                <i class="fas fa-plus-circle"></i>
                <span>Add New Medicine</span>
            </a>
        </div>
    </div>

    {{-- Search & Filter Bar --}}
    <div class="med-toolbar">
        <form action="{{ route('medicines.index') }}" method="GET" class="med-filters">
            <div class="search-wrapper">
                <i class="fas fa-search"></i>
                <input type="text" name="search" class="search-input" 
                       placeholder="Search by name, brand, generic, or batch number..."
                       value="{{ request('search') }}" autocomplete="off">
                @if(request('search'))
                <a href="{{ route('medicines.index', request()->except('search','page')) }}" class="search-clear">
                    <i class="fas fa-times"></i>
                </a>
                @endif
            </div>

            <div class="filter-group">
                <select name="category_id" class="filter-select">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>

                <select name="category_type" class="filter-select">
                    <option value="">All Ages</option>
                    <option value="adults" {{ request('category_type') == 'adults' ? 'selected' : '' }}>Adults Only</option>
                    <option value="children" {{ request('category_type') == 'children' ? 'selected' : '' }}>Children Only</option>
                    <option value="both" {{ request('category_type') == 'both' ? 'selected' : '' }}>Both</option>
                </select>

                <select name="status" class="filter-select">
                    <option value="">All Status</option>
                    <option value="in_stock" {{ request('status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                    <option value="low_stock" {{ request('status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                    <option value="out_of_stock" {{ request('status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                </select>

                <button type="submit" class="btn-filter">
                    <i class="fas fa-sliders-h"></i> Apply
                </button>

                @if(request()->hasAny(['search','category_id','category_type','status']))
                    <a href="{{ route('medicines.index') }}" class="btn-clear">
                        <i class="fas fa-times"></i> Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Table View --}}
    @if($medicines->isEmpty())
        <div class="empty-state">
            <i class="fas fa-capsules"></i>
            <h4>No medicines found</h4>
            <p>Try adjusting your search filters or add a new medicine to get started.</p>
            <a href="{{ route('medicines.create') }}" class="btn-modern btn-primary">
                <i class="fas fa-plus-circle"></i> Add Medicine
            </a>
        </div>
    @else
        <div class="table-container">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Medicine</th>
                        <th>Category</th>
                        <th>Stock</th>
                        <th>Price</th>
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
                            $typeLabels = ['adults' => 'Adults', 'children' => 'Children', 'both' => 'All ages'];
                            $typeColors = ['adults' => 'blue', 'children' => 'green', 'both' => 'teal'];
                            
                            // Format stock display based on unit/dosage form (matching mobile app logic)
                            $unit = $medicine->unit ?? '';
                            $strength = $medicine->strength ?? '';
                            $quantity = $medicine->quantity;
                            
                            // Unit types that are counted as individual pieces (tablets/capsules)
                            $countableUnits = ['tablet', 'capsule', 'caplet', 'pill', 'softgel', 'lozenge'];
                            // Unit types that are measured in bottles/containers (liquids, powders, granules)
                            $bottledUnits = ['ml', 'mg', 'g', 'mcg', 'iu', 'liquid', 'syrup', 'suspension', 'solution', 'drops', 'powder', 'granule'];
                            
                            if (in_array($unit, $countableUnits)) {
                                // For tablets/capsules - show count with proper pluralization
                                $unitLabel = $quantity == 1 ? rtrim($unit, 's') : $unit . 's';
                                $stockDisplay = number_format($quantity) . ' ' . $unitLabel;
                                $stockIcon = '<i class="fas fa-tablets"></i>';
                                $stockType = 'Countable';
                            } elseif (in_array($unit, $bottledUnits)) {
                                // For liquids, powders, granules - show number of bottles/containers
                                $stockDisplay = number_format($quantity) . ' bottle(s)';
                                if ($strength) {
                                    $stockDisplay .= ' (' . $strength . ' ' . $unit . ')';
                                }
                                $stockIcon = '<i class="fas fa-flask"></i>';
                                $stockType = 'Bottled/Container';
                            } else {
                                // Default display
                                $stockDisplay = number_format($quantity) . ' ' . ($unit ?: 'units');
                                $stockIcon = '<i class="fas fa-capsules"></i>';
                                $stockType = 'Standard';
                            }
                            
                            // Get dosage form icon
                            $dosageIcons = [
                                'tablet' => 'fa-tablet-alt',
                                'capsule' => 'fa-capsules',
                                'syrup' => 'fa-flask',
                                'suspension' => 'fa-flask',
                                'drops' => 'fa-tint',
                                'injection' => 'fa-syringe',
                                'powder' => 'fa-dice-d6',
                                'granule' => 'fa-dice-d6',
                            ];
                            $dosageIcon = $dosageIcons[$medicine->dosage_form] ?? 'fa-capsules';
                        @endphp
                        <tr class="{{ $isExpired ? 'expired-row' : ($medicine->status === 'low_stock' ? 'low-stock-row' : '') }}">
                            <td>
                                <div class="medicine-info">
                                    <div class="medicine-icon">
                                        <i class="fas {{ $dosageIcon }}"></i>
                                    </div>
                                    <div>
                                        <div class="medicine-name">
                                            {{ $medicine->name }}
                                            @if($medicine->requires_prescription)
                                                <span class="rx-badge">Rx</span>
                                            @endif
                                        </div>
                                        <div class="medicine-meta">
                                            @if($medicine->brand)
                                                <span>{{ $medicine->brand }}</span>
                                                <span class="separator">•</span>
                                            @endif
                                            <span>{{ $medicine->generic_name }}</span>
                                            @if($medicine->strength && $medicine->unit)
                                                <span class="separator">•</span>
                                                <span>{{ $medicine->strength }} {{ $medicine->unit }}</span>
                                            @endif
                                        </div>
                                        @if($medicine->category_type)
                                            <div class="age-badge age-{{ $typeColors[$medicine->category_type] }}">
                                                {{ $typeLabels[$medicine->category_type] }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <span class="category-badge">{{ $medicine->category->name ?? 'Uncategorized' }}</span>
                                    @if($medicine->supplier)
                                        <div class="supplier-name">
                                            <i class="fas fa-truck"></i> {{ $medicine->supplier->name }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="stock-cell">
                                    <div class="stock-header">
                                        <span class="stock-icon">{!! $stockIcon !!}</span>
                                        <span class="stock-number {{ $medicine->quantity == 0 ? 'critical' : ($medicine->status === 'low_stock' ? 'warning' : 'normal') }}">
                                            {{ $stockDisplay }}
                                        </span>
                                    </div>
                                    <div class="stock-bar">
                                        <div class="stock-fill" style="width: {{ $medicine->reorder_level > 0 ? min(100, ($medicine->quantity / ($medicine->reorder_level * 2)) * 100) : 100 }}%"></div>
                                    </div>
                                    <div class="stock-reorder">
                                        <i class="fas fa-chart-line"></i> Reorder: {{ number_format($medicine->reorder_level) }} 
                                        @if(in_array($unit, $bottledUnits))
                                            bottle(s)
                                        @else
                                            {{ $unit ?: 'units' }}
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="price-cell">
                                    <div class="selling-price">₱{{ number_format($medicine->selling_price, 2) }}</div>
                                    <div class="purchase-price">₱{{ number_format($medicine->purchase_price, 2) }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="expiry-cell {{ $isExpired ? 'expired' : ($daysLeft <= 30 ? 'expiring' : '') }}">
                                    <div class="expiry-date">
                                        {{ $medicine->expiry_date->format('M d, Y') }}
                                    </div>
                                    @if($isExpired)
                                        <span class="expiry-badge expired">Expired {{ abs($daysLeft) }}d ago</span>
                                    @elseif($daysLeft <= 30)
                                        <span class="expiry-badge warning">{{ $daysLeft }} days left</span>
                                    @else
                                        <span class="expiry-badge good">{{ $daysLeft }} days</span>
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
                                    <a href="{{ route('medicines.show', $medicine) }}" class="action-btn view-btn" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('medicines.edit', $medicine) }}" class="action-btn edit-btn" title="Edit Medicine">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="pagination-modern">
            <div class="pagination-info">
                Showing {{ $medicines->firstItem() }}–{{ $medicines->lastItem() }} of {{ $medicines->total() }} medicines
            </div>
            {{ $medicines->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
/* Modern Medicines Table CSS */
.medicines-modern {
    max-width: 1600px;
    margin: 0 auto;
    padding: 2rem;
}

/* Hero Section */
.med-hero {
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    border-radius: 24px;
    padding: 2rem;
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
}

.med-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(37, 99, 235, 0.1) 0%, transparent 70%);
    pointer-events: none;
}

.med-hero-content {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 2rem;
    position: relative;
    z-index: 1;
}

.med-badge {
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

.med-title {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    color: white;
}

.med-subtitle {
    color: rgba(255, 255, 255, 0.7);
    margin: 0;
}

.stat-chip {
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

.med-actions {
    display: flex;
    gap: 1rem;
    position: relative;
    z-index: 1;
}

/* Buttons */
.btn-modern {
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
    text-decoration: none;
}

.btn-primary {
    background: #2563eb;
    color: white;
}

.btn-primary:hover {
    background: #1d4ed8;
    transform: translateY(-2px);
}

/* Toolbar */
.med-toolbar {
    margin-bottom: 2rem;
}

.med-filters {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    align-items: center;
}

.search-wrapper {
    flex: 1;
    min-width: 300px;
    position: relative;
}

.search-wrapper i {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
}

.search-input {
    width: 100%;
    padding: 0.75rem 1rem 0.75rem 2.5rem;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    font-size: 0.875rem;
    transition: all 0.2s;
    background: white;
}

.search-input:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.search-clear {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    text-decoration: none;
}

.search-clear:hover {
    color: #ef4444;
}

.filter-group {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.filter-select {
    padding: 0.75rem 1rem;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    font-size: 0.875rem;
    background: white;
    cursor: pointer;
    transition: all 0.2s;
}

.filter-select:focus {
    outline: none;
    border-color: #2563eb;
}

.btn-filter {
    padding: 0.75rem 1.5rem;
    background: #2563eb;
    color: white;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.875rem;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-filter:hover {
    background: #1d4ed8;
    transform: translateY(-1px);
}

.btn-clear {
    padding: 0.75rem 1.5rem;
    background: transparent;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.875rem;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    color: #64748b;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-clear:hover {
    background: #f1f5f9;
    border-color: #cbd5e1;
    color: #ef4444;
}

/* Table Container */
.table-container {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 1px solid #e2e8f0;
    margin-bottom: 1.5rem;
}

.modern-table {
    width: 100%;
    border-collapse: collapse;
}

.modern-table thead {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
}

.modern-table th {
    padding: 1rem 1.25rem;
    text-align: left;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #64748b;
}

.modern-table td {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
}

.modern-table tbody tr:hover {
    background: #f8fafc;
}

.modern-table tbody tr.expired-row {
    background: #fef2f2;
}

.modern-table tbody tr.low-stock-row {
    background: #fffbeb;
}

/* Medicine Info */
.medicine-info {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
}

.medicine-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #2563eb, #1e40af);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
}

.medicine-name {
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 0.25rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.rx-badge {
    background: #ef4444;
    color: white;
    padding: 0.125rem 0.375rem;
    border-radius: 4px;
    font-size: 0.6rem;
    font-weight: 700;
}

.medicine-meta {
    font-size: 0.75rem;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 0.25rem;
    flex-wrap: wrap;
}

.separator {
    color: #cbd5e1;
}

.age-badge {
    display: inline-block;
    padding: 0.125rem 0.5rem;
    border-radius: 4px;
    font-size: 0.65rem;
    font-weight: 600;
    margin-top: 0.25rem;
}

.age-blue { background: #dbeafe; color: #1e40af; }
.age-green { background: #dcfce7; color: #166534; }
.age-teal { background: #ccfbf1; color: #0f766e; }

/* Category */
.category-badge {
    background: #f1f5f9;
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 500;
    color: #475569;
    display: inline-block;
}

.supplier-name {
    font-size: 0.7rem;
    color: #94a3b8;
    margin-top: 0.25rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

/* Stock Cell */
.stock-cell {
    min-width: 160px;
}

.stock-header {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.25rem;
}

.stock-icon {
    color: #64748b;
}

.stock-number {
    font-weight: 700;
    font-size: 0.9rem;
}

.stock-number.critical { color: #ef4444; }
.stock-number.warning { color: #f59e0b; }
.stock-number.normal { color: #10b981; }

.stock-bar {
    height: 4px;
    background: #e2e8f0;
    border-radius: 2px;
    overflow: hidden;
    margin: 0.5rem 0;
}

.stock-fill {
    height: 100%;
    background: #10b981;
    border-radius: 2px;
    transition: width 0.3s;
}

.stock-reorder {
    font-size: 0.65rem;
    color: #94a3b8;
    margin-top: 0.25rem;
}

.stock-reorder i {
    font-size: 0.6rem;
    margin-right: 0.25rem;
}

/* Price Cell */
.price-cell {
    text-align: left;
}

.selling-price {
    font-weight: 700;
    color: #0f172a;
    font-size: 0.875rem;
}

.purchase-price {
    font-size: 0.7rem;
    color: #94a3b8;
    text-decoration: line-through;
}

/* Expiry Cell */
.expiry-cell {
    text-align: left;
}

.expiry-date {
    font-weight: 500;
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
}

.expiry-badge {
    font-size: 0.65rem;
    padding: 0.125rem 0.375rem;
    border-radius: 4px;
    display: inline-block;
}

.expiry-badge.expired {
    background: #fee2e2;
    color: #991b1b;
}

.expiry-badge.warning {
    background: #fed7aa;
    color: #9a3412;
}

.expiry-badge.good {
    background: #dcfce7;
    color: #166534;
}

.expiry-cell.expired .expiry-date {
    color: #ef4444;
}

.expiry-cell.expiring .expiry-date {
    color: #f59e0b;
}

/* Status Badge */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 600;
    white-space: nowrap;
}

.status-in_stock { background: #dcfce7; color: #15803d; }
.status-low_stock { background: #fef3c7; color: #a16207; }
.status-out_of_stock { background: #f1f5f9; color: #475569; }
.status-expired { background: #fee2e2; color: #991b1b; }

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 0.5rem;
}

.action-btn {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    background: white;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: all 0.2s;
    color: #64748b;
}

.action-btn:hover {
    transform: translateY(-2px);
}

.view-btn:hover {
    border-color: #2563eb;
    color: #2563eb;
    background: #eff6ff;
}

.edit-btn:hover {
    border-color: #f59e0b;
    color: #f59e0b;
    background: #fffbeb;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 4rem;
    background: white;
    border-radius: 20px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 1px solid #e2e8f0;
}

.empty-state i {
    font-size: 4rem;
    color: #cbd5e1;
    margin-bottom: 1rem;
}

.empty-state h4 {
    margin: 0 0 0.5rem 0;
    font-size: 1.25rem;
    color: #0f172a;
}

.empty-state p {
    margin: 0 0 1.5rem 0;
    color: #64748b;
}

/* Pagination */
.pagination-modern {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    padding: 1rem 0;
}

.pagination-info {
    font-size: 0.875rem;
    color: #64748b;
}

.pagination-modern nav {
    display: inline-block;
}

.pagination-modern .pagination {
    margin: 0;
    display: flex;
    gap: 0.25rem;
}

.pagination-modern .page-item .page-link {
    padding: 0.5rem 0.75rem;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    color: #475569;
    transition: all 0.2s;
}

.pagination-modern .page-item.active .page-link {
    background: #2563eb;
    border-color: #2563eb;
    color: white;
}

.pagination-modern .page-item .page-link:hover:not(.active) {
    background: #f1f5f9;
    border-color: #cbd5e1;
}

/* Responsive */
@media (max-width: 1024px) {
    .modern-table {
        display: block;
        overflow-x: auto;
    }
}

@media (max-width: 768px) {
    .medicines-modern {
        padding: 1rem;
    }
    
    .med-hero-content {
        flex-direction: column;
        gap: 1rem;
    }
    
    .med-actions {
        flex-direction: column;
    }
    
    .med-filters {
        flex-direction: column;
    }
    
    .search-wrapper {
        min-width: 100%;
    }
    
    .filter-group {
        width: 100%;
    }
    
    .filter-select,
    .btn-filter,
    .btn-clear {
        flex: 1;
    }
    
    .pagination-modern {
        flex-direction: column;
        text-align: center;
    }
}
</style>
@endpush
@extends('layouts.app')

@section('title', 'Sales History')

@section('content')
<div class="sales-modern">
    {{-- Hero Section --}}
    <div class="sales-hero">
        <div class="sales-hero-content">
            <div class="sales-hero-left">
                <div class="sales-badge">Transaction Records</div>
                <h1 class="sales-title">Sales History</h1>
                <p class="sales-subtitle">View and manage all sales transactions</p>
            </div>
            <div class="sales-hero-right">
                <div class="stats-chip">
                    <i class="fas fa-chart-line"></i>
                    <span>Total Sales: ₱{{ number_format($sales->sum('grand_total'), 2) }}</span>
                </div>
            </div>
        </div>
        <div class="sales-actions">
            <a href="{{ route('sales.create') }}" class="btn-modern btn-primary">
                <i class="fas fa-plus-circle"></i>
                <span>New Sale</span>
            </a>
        </div>
    </div>

    {{-- Filters Card --}}
    <div class="filters-card">
        <div class="filters-header">
            <i class="fas fa-filter"></i>
            <h3>Filter Transactions</h3>
        </div>
        <form action="{{ route('sales.index') }}" method="GET" class="filters-form">
            <div class="filters-grid">
                <div class="filter-group">
                    <label>Date From</label>
                    <div class="date-input">
                        <i class="fas fa-calendar-alt"></i>
                        <input type="date" name="date_from" value="{{ request('date_from') }}">
                    </div>
                </div>
                <div class="filter-group">
                    <label>Date To</label>
                    <div class="date-input">
                        <i class="fas fa-calendar-alt"></i>
                        <input type="date" name="date_to" value="{{ request('date_to') }}">
                    </div>
                </div>
                <div class="filter-group">
                    <label>Invoice Number</label>
                    <div class="search-input">
                        <i class="fas fa-hashtag"></i>
                        <input type="text" name="invoice" placeholder="Search invoice..." value="{{ request('invoice') }}">
                    </div>
                </div>
                <div class="filter-actions">
                    <button type="submit" class="btn-apply">
                        <i class="fas fa-sliders-h"></i> Apply Filters
                    </button>
                    @if(request()->hasAny(['date_from', 'date_to', 'invoice']))
                    <a href="{{ route('sales.index') }}" class="btn-clear">
                        <i class="fas fa-times"></i> Clear
                    </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- Sales Table --}}
    <div class="sales-table-container">
        @if($sales->isEmpty())
            <div class="empty-state">
                <i class="fas fa-shopping-cart"></i>
                <h4>No sales found</h4>
                <p>Start by creating your first sale transaction</p>
                <a href="{{ route('sales.create') }}" class="btn-modern btn-primary">
                    <i class="fas fa-plus-circle"></i> New Sale
                </a>
            </div>
        @else
            <div class="table-wrapper">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Date & Time</th>
                            <th>Cashier</th>
                            <th>Items</th>
                            <th>Subtotal</th>
                            <th>Discount</th>
                            <th>Tax</th>
                            <th>Total</th>
                            <th>Payment</th>
                            <th>Actions</th>
                        </thead>
                    <tbody>
                        @foreach($sales as $sale)
                        <tr class="sale-row">
                            <td>
                                <span class="invoice-badge">#{{ $sale->invoice_number }}</span>
                            </td>
                            <td class="date-cell">
                                <div class="date-main">{{ $sale->created_at->format('M d, Y') }}</div>
                                <div class="date-time">{{ $sale->created_at->format('h:i A') }}</div>
                            </td>
                            <td class="cashier-cell">
                                <div class="cashier-avatar">
                                    {{ strtoupper(substr($sale->user->name, 0, 1)) }}
                                </div>
                                <span>{{ $sale->user->name }}</span>
                            </td>
                            <td>
                                <span class="items-badge">{{ $sale->items->count() }}</span>
                            </td>
                            <td class="amount">₱{{ number_format($sale->total_amount, 2) }}</td>
                            <td class="discount">
                                @if($sale->discount > 0)
                                    <span class="discount-badge">-₱{{ number_format($sale->discount, 2) }}</span>
                                @else
                                    <span class="no-discount">—</span>
                                @endif
                            </td>
                            <td class="tax">₱{{ number_format($sale->tax, 2) }}</td>
                            <td class="total">₱{{ number_format($sale->grand_total, 2) }}</td>
                            <td>
                                @php
                                    $paymentColors = [
                                        'cash' => 'cash',
                                        'card' => 'card',
                                        'mobile_payment' => 'mobile'
                                    ];
                                    $paymentIcons = [
                                        'cash' => 'money-bill-wave',
                                        'card' => 'credit-card',
                                        'mobile_payment' => 'mobile-alt'
                                    ];
                                @endphp
                                <span class="payment-badge payment-{{ $paymentColors[$sale->payment_method] ?? 'default' }}">
                                    <i class="fas fa-{{ $paymentIcons[$sale->payment_method] ?? 'receipt' }}"></i>
                                    {{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}
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
                                    <button type="button" class="action-icon delete" title="Delete Sale" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $sale->id }}">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
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
                    <i class="fas fa-info-circle"></i>
                    Showing {{ $sales->firstItem() }} to {{ $sales->lastItem() }} of {{ $sales->total() }} entries
                </div>
                <div class="pagination-links">
                    {{ $sales->withQueryString()->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

{{-- Delete Modals --}}
@foreach($sales as $sale)
<div class="modal fade" id="deleteModal{{ $sale->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                                <div class="modal-icon">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>
                                <h5 class="modal-title">Delete Sale</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p>Are you sure you want to delete sale <strong>{{ $sale->invoice_number }}</strong>?</p>
                                <div class="alert-warning-box">
                                    <i class="fas fa-info-circle"></i>
                                    <span>This action will restore all medicine stocks and cannot be undone.</span>
                                </div>
                                <div class="sale-summary">
                                    <div class="summary-item">
                                        <span>Date:</span>
                                        <strong>{{ $sale->created_at->format('M d, Y h:i A') }}</strong>
                                    </div>
                                    <div class="summary-item">
                                        <span>Items:</span>
                                        <strong>{{ $sale->items->count() }}</strong>
                                    </div>
                                    <div class="summary-item">
                                        <span>Total:</span>
                                        <strong class="text-danger">₱{{ number_format($sale->grand_total, 2) }}</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn-cancel-modal" data-bs-dismiss="modal">Cancel</button>
                                <form action="{{ route('sales.destroy', $sale) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete-modal">
                                        <i class="fas fa-trash-alt"></i> Delete Permanently
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach

                @push('styles')
                <style>
                /* Sales Modern CSS */
                .sales-modern {
                    max-width: 1600px;
                    margin: 0 auto;
                    padding: 2rem;
                }

                /* Hero Section */
                .sales-hero {
                    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
                    border-radius: 24px;
                    padding: 2rem;
                    margin-bottom: 2rem;
                    position: relative;
                    overflow: hidden;
                    display: flex;
                    justify-content: space-between;
                    align-items: flex-start;
                }

                .sales-hero::before {
                    content: '';
                    position: absolute;
                    top: -50%;
                    right: -50%;
                    width: 200%;
                    height: 200%;
                    background: radial-gradient(circle, rgba(37, 99, 235, 0.1) 0%, transparent 70%);
                    pointer-events: none;
                }

                .sales-badge {
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

                .sales-title {
                    font-size: 2rem;
                    font-weight: 700;
                    margin-bottom: 0.5rem;
                    color: white;
                }

                .sales-subtitle {
                    color: rgba(255, 255, 255, 0.7);
                    margin: 0;
                }

                .stats-chip {
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

                .sales-actions {
                    position: absolute;
                    top: 2rem;
                    right: 2rem;
                }

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
                    background: #10b981;
                    color: white;
                }

                .btn-primary:hover {
                    background: #059669;
                    transform: translateY(-2px);
                }

                /* Filters Card */
                .filters-card {
                    background: white;
                    border-radius: 20px;
                    border: 1px solid #e2e8f0;
                    margin-bottom: 1.5rem;
                    overflow: hidden;
                    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
                }

                .filters-header {
                    padding: 1rem 1.5rem;
                    background: #f8fafc;
                    border-bottom: 1px solid #e2e8f0;
                    display: flex;
                    align-items: center;
                    gap: 0.75rem;
                }

                .filters-header i {
                    font-size: 1rem;
                    color: #2563eb;
                }

                .filters-header h3 {
                    margin: 0;
                    font-size: 0.875rem;
                    font-weight: 600;
                    color: #0f172a;
                }

                .filters-form {
                    padding: 1.5rem;
                }

                .filters-grid {
                    display: grid;
                    grid-template-columns: repeat(4, 1fr);
                    gap: 1rem;
                    align-items: end;
                }

                .filter-group {
                    display: flex;
                    flex-direction: column;
                    gap: 0.5rem;
                }

                .filter-group label {
                    font-size: 0.7rem;
                    font-weight: 600;
                    text-transform: uppercase;
                    color: #64748b;
                    letter-spacing: 0.5px;
                }

                .date-input,
                .search-input {
                    position: relative;
                    display: flex;
                    align-items: center;
                }

                .date-input i,
                .search-input i {
                    position: absolute;
                    left: 0.75rem;
                    color: #94a3b8;
                    font-size: 0.875rem;
                }

                .date-input input,
                .search-input input {
                    width: 100%;
                    padding: 0.625rem 0.75rem 0.625rem 2.25rem;
                    border: 1.5px solid #e2e8f0;
                    border-radius: 12px;
                    font-size: 0.875rem;
                    transition: all 0.2s;
                }

                .date-input input:focus,
                .search-input input:focus {
                    outline: none;
                    border-color: #2563eb;
                    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
                }

                .filter-actions {
                    display: flex;
                    gap: 0.5rem;
                    align-items: center;
                }

                .btn-apply {
                    padding: 0.625rem 1.25rem;
                    background: #2563eb;
                    color: white;
                    border: none;
                    border-radius: 40px;
                    font-weight: 600;
                    font-size: 0.75rem;
                    cursor: pointer;
                    transition: all 0.2s;
                    display: inline-flex;
                    align-items: center;
                    gap: 0.5rem;
                }

                .btn-apply:hover {
                    background: #1d4ed8;
                    transform: translateY(-1px);
                }

                .btn-clear {
                    padding: 0.625rem 1.25rem;
                    background: transparent;
                    border: 1px solid #e2e8f0;
                    border-radius: 40px;
                    font-weight: 600;
                    font-size: 0.75rem;
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
                .sales-table-container {
                    background: white;
                    border-radius: 20px;
                    border: 1px solid #e2e8f0;
                    overflow: hidden;
                    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
                }

                .table-wrapper {
                    overflow-x: auto;
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
                    padding: 1rem;
                    text-align: left;
                    font-size: 0.7rem;
                    font-weight: 600;
                    text-transform: uppercase;
                    letter-spacing: 0.05em;
                    color: #64748b;
                    white-space: nowrap;
                }

                .modern-table td {
                    padding: 1rem;
                    border-bottom: 1px solid #f1f5f9;
                    vertical-align: middle;
                }

                .modern-table tbody tr:hover {
                    background: #f8fafc;
                }

                /* Table Styles */
                .invoice-badge {
                    background: #f1f5f9;
                    color: #475569;
                    padding: 0.25rem 0.5rem;
                    border-radius: 6px;
                    font-size: 0.75rem;
                    font-weight: 600;
                    font-family: monospace;
                }

                .date-cell {
                    font-size: 0.875rem;
                }

                .date-main {
                    font-weight: 500;
                    margin-bottom: 0.125rem;
                }

                .date-time {
                    font-size: 0.7rem;
                    color: #94a3b8;
                }

                .cashier-cell {
                    display: flex;
                    align-items: center;
                    gap: 0.5rem;
                }

                .cashier-avatar {
                    width: 32px;
                    height: 32px;
                    background: linear-gradient(135deg, #2563eb, #1e40af);
                    border-radius: 8px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: white;
                    font-size: 0.75rem;
                    font-weight: 600;
                }

                .items-badge {
                    background: #e0f2fe;
                    color: #0284c7;
                    padding: 0.25rem 0.5rem;
                    border-radius: 20px;
                    font-size: 0.7rem;
                    font-weight: 600;
                }

                .amount,
                .tax,
                .total {
                    font-size: 0.875rem;
                }

                .total {
                    font-weight: 700;
                    color: #10b981;
                }

                .discount-badge {
                    color: #ef4444;
                    font-size: 0.75rem;
                    font-weight: 500;
                }

                .no-discount {
                    color: #94a3b8;
                    font-size: 0.75rem;
                }

                /* Payment Badges */
                .payment-badge {
                    display: inline-flex;
                    align-items: center;
                    gap: 0.375rem;
                    padding: 0.25rem 0.625rem;
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

                .payment-mobile {
                    background: #e0e7ff;
                    color: #4338ca;
                }

                /* Action Buttons */
                .action-buttons {
                    display: flex;
                    gap: 0.5rem;
                }

                .action-icon {
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
                    cursor: pointer;
                }

                .action-icon.view:hover {
                    border-color: #2563eb;
                    color: #2563eb;
                    background: #eff6ff;
                    transform: translateY(-2px);
                }

                .action-icon.print:hover {
                    border-color: #10b981;
                    color: #10b981;
                    background: #dcfce7;
                    transform: translateY(-2px);
                }

                .action-icon.delete:hover {
                    border-color: #ef4444;
                    color: #ef4444;
                    background: #fee2e2;
                    transform: translateY(-2px);
                }

                /* Empty State */
                .empty-state {
                    text-align: center;
                    padding: 4rem;
                }

                .empty-state i {
                    font-size: 4rem;
                    color: #cbd5e1;
                    margin-bottom: 1rem;
                }

                .empty-state h4 {
                    margin: 0 0 0.5rem 0;
                    font-size: 1.125rem;
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
                    padding: 1rem 1.5rem;
                    border-top: 1px solid #e2e8f0;
                    flex-wrap: wrap;
                    gap: 1rem;
                }

                .pagination-info {
                    display: flex;
                    align-items: center;
                    gap: 0.5rem;
                    font-size: 0.75rem;
                    color: #64748b;
                }

                .pagination-info i {
                    color: #2563eb;
                }

                .pagination-links .pagination {
                    margin: 0;
                    display: flex;
                    gap: 0.25rem;
                }

                .pagination-links .page-item .page-link {
                    padding: 0.375rem 0.75rem;
                    border-radius: 8px;
                    border: 1px solid #e2e8f0;
                    color: #475569;
                    font-size: 0.75rem;
                    transition: all 0.2s;
                }

                .pagination-links .page-item.active .page-link {
                    background: #2563eb;
                    border-color: #2563eb;
                    color: white;
                }

                .pagination-links .page-item .page-link:hover:not(.active) {
                    background: #f1f5f9;
                    border-color: #cbd5e1;
                }

                /* Modal Styles */
                .modal-content {
                    border-radius: 20px;
                    border: none;
                    overflow: hidden;
                }

                .modal-header {
                    background: #f8fafc;
                    border-bottom: 1px solid #e2e8f0;
                    padding: 1.25rem 1.5rem;
                    display: flex;
                    align-items: center;
                    gap: 0.75rem;
                }

                .modal-icon {
                    width: 40px;
                    height: 40px;
                    background: #fee2e2;
                    border-radius: 12px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }

                .modal-icon i {
                    font-size: 1.25rem;
                    color: #ef4444;
                }

                .modal-title {
                    font-size: 1rem;
                    font-weight: 600;
                    margin: 0;
                }

                .alert-warning-box {
                    background: #fef3c7;
                    border-left: 3px solid #f59e0b;
                    padding: 0.75rem 1rem;
                    border-radius: 12px;
                    margin: 1rem 0;
                    display: flex;
                    align-items: center;
                    gap: 0.5rem;
                    font-size: 0.75rem;
                    color: #92400e;
                }

                .sale-summary {
                    background: #f8fafc;
                    border-radius: 12px;
                    padding: 1rem;
                }

                .summary-item {
                    display: flex;
                    justify-content: space-between;
                    padding: 0.5rem 0;
                    border-bottom: 1px solid #e2e8f0;
                    font-size: 0.875rem;
                }

                .summary-item:last-child {
                    border-bottom: none;
                }

                .modal-footer {
                    padding: 1rem 1.5rem;
                    border-top: 1px solid #e2e8f0;
                    display: flex;
                    justify-content: flex-end;
                    gap: 0.75rem;
                }

                .btn-cancel-modal {
                    padding: 0.5rem 1rem;
                    background: transparent;
                    border: 1px solid #e2e8f0;
                    border-radius: 40px;
                    font-size: 0.75rem;
                    font-weight: 600;
                    cursor: pointer;
                    transition: all 0.2s;
                }

                .btn-cancel-modal:hover {
                    background: #f1f5f9;
                }

                .btn-delete-modal {
                    padding: 0.5rem 1rem;
                    background: #ef4444;
                    border: none;
                    border-radius: 40px;
                    color: white;
                    font-size: 0.75rem;
                    font-weight: 600;
                    cursor: pointer;
                    transition: all 0.2s;
                    display: inline-flex;
                    align-items: center;
                    gap: 0.5rem;
                }

                .btn-delete-modal:hover {
                    background: #dc2626;
                    transform: translateY(-1px);
                }

                /* Responsive */
                @media (max-width: 1024px) {
                    .sales-modern {
                        padding: 1rem;
                    }
                    
                    .filters-grid {
                        grid-template-columns: repeat(2, 1fr);
                    }
                }

                @media (max-width: 768px) {
                    .sales-hero {
                        flex-direction: column;
                        gap: 1rem;
                    }
                    
                    .sales-actions {
                        position: static;
                        margin-top: 1rem;
                    }
                    
                    .filters-grid {
                        grid-template-columns: 1fr;
                    }
                    
                    .filter-actions {
                        flex-direction: column;
                    }
                    
                    .btn-apply,
                    .btn-clear {
                        width: 100%;
                        justify-content: center;
                    }
                    
                    .modern-table {
                        font-size: 0.75rem;
                    }
                    
                    .modern-table td,
                    .modern-table th {
                        padding: 0.5rem;
                    }
                    
                    .pagination-modern {
                        flex-direction: column;
                        text-align: center;
                    }
                }
                </style>
                @endpush

                @push('scripts')
                <script>
                // Auto-hide tooltips
                document.addEventListener('DOMContentLoaded', function() {
                    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                        return new bootstrap.Tooltip(tooltipTriggerEl);
                    });
                });
                </script>
                @endpush
                @endsection
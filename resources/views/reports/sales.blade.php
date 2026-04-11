@extends('layouts.app')

@section('title', 'Sales Report')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="fw-bold">
                    <i class="fas fa-chart-line text-primary me-2"></i>
                    Sales Report
                </h2>
                <div>
                    
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="text-white-50">Total Transactions</h6>
                    <h3 class="mb-0">{{ $totalSales }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="text-white-50">Total Revenue</h6>
                    <h3 class="mb-0">₱{{ number_format($totalRevenue, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="text-white-50">Items Sold</h6>
                    <h3 class="mb-0">{{ $totalItems }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Report</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('reports.sales') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Date From</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Date To</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Payment Method</label>
                    <select name="payment_method" class="form-select">
                        <option value="">All Methods</option>
                        <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="card" {{ request('payment_method') == 'card' ? 'selected' : '' }}>Card</option>
                        <option value="mobile_payment" {{ request('payment_method') == 'mobile_payment' ? 'selected' : '' }}>Mobile Payment</option>
                    </select>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                @if(request()->hasAny(['date_from', 'date_to', 'payment_method']))
                <div class="col-12 text-end">
                    <a href="{{ route('reports.sales') }}" class="btn btn-link text-danger">
                        <i class="fas fa-times-circle me-1"></i>Clear Filters
                    </a>
                </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Payment Method Stats -->
    @if($paymentMethodStats->isNotEmpty())
    <div class="row mb-4">
        @foreach($paymentMethodStats as $stat)
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">{{ ucfirst(str_replace('_', ' ', $stat->payment_method)) }}</h6>
                    <div class="d-flex justify-content-between">
                        <span>{{ $stat->count }} transactions</span>
                        <strong>₱{{ number_format($stat->total, 2) }}</strong>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Sales Table -->
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Sales Transactions</h5>
        </div>
        <div class="card-body">
            @if($sales->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-chart-line fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">No sales found</h5>
                    <p class="text-muted">Try adjusting your filters.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
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
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sales as $sale)
                            <tr>
                                <td>
                                    <span class="badge bg-secondary">#{{ $sale->invoice_number }}</span>
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $sale->created_at->format('M d, Y') }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $sale->created_at->format('h:i A') }}</small>
                                    </div>
                                </td>
                                <td>{{ $sale->user->name }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $sale->items->count() }}</span>
                                </td>
                                <td>₱{{ number_format($sale->total_amount, 2) }}</td>
                                <td>
                                    @if($sale->discount > 0)
                                        <span class="text-danger">-₱{{ number_format($sale->discount, 2) }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>₱{{ number_format($sale->tax, 2) }}</td>
                                <td>
                                    <strong class="text-primary">₱{{ number_format($sale->grand_total, 2) }}</strong>
                                </td>
                                <td>
                                    @php
                                        $paymentColors = [
                                            'cash' => 'success',
                                            'card' => 'primary',
                                            'mobile_payment' => 'info'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $paymentColors[$sale->payment_method] ?? 'secondary' }}">
                                        {{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('sales.show', $sale) }}" 
                                       class="btn btn-sm btn-outline-info" 
                                       title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('sales.invoice', $sale) }}" 
                                       target="_blank"
                                       class="btn btn-sm btn-outline-success" 
                                       title="Print Invoice">
                                        <i class="fas fa-print"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div>
                        <p class="text-muted small mb-0">
                            Showing <strong>{{ $sales->firstItem() }}</strong> to 
                            <strong>{{ $sales->lastItem() }}</strong> of 
                            <strong>{{ $sales->total() }}</strong> entries
                        </p>
                    </div>
                    <div>
                        {{ $sales->withQueryString()->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
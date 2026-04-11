@extends('layouts.app')

@section('title', 'Expiry Report')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="fw-bold">
                    <i class="fas fa-clock text-danger me-2"></i>
                    Expiry Report
                </h2>
                <div>
                   
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h6 class="text-white-50">Expired</h6>
                    <h3 class="mb-0">{{ $expired }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h6 class="text-dark-50">Next 30 Days</h6>
                    <h3 class="mb-0">{{ $expiringNextMonth }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="text-white-50">Next 3 Months</h6>
                    <h3 class="mb-0">{{ $expiringNext3Months }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="text-white-50">Next 6 Months</h6>
                    <h3 class="mb-0">{{ $expiringNext6Months }}</h3>
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
            <form action="{{ route('reports.expiry') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Expiry Period</label>
                    <select name="expiry_period" class="form-select">
                        <option value="">All (Next 6 Months)</option>
                        <option value="1month" {{ request('expiry_period') == '1month' ? 'selected' : '' }}>Next 30 Days</option>
                        <option value="3months" {{ request('expiry_period') == '3months' ? 'selected' : '' }}>Next 3 Months</option>
                        <option value="6months" {{ request('expiry_period') == '6months' ? 'selected' : '' }}>Next 6 Months</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date From</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Date To</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                @if(request()->hasAny(['expiry_period', 'category_id', 'date_from', 'date_to']))
                <div class="col-12 text-end">
                    <a href="{{ route('reports.expiry') }}" class="btn btn-link text-danger">
                        <i class="fas fa-times-circle me-1"></i>Clear Filters
                    </a>
                </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Expiry Table -->
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Medicines Expiring Soon</h5>
        </div>
        <div class="card-body">
            @if($medicines->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                    <h5 class="text-muted">No expiring medicines found!</h5>
                    <p class="text-muted">All medicines have valid expiry dates.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Medicine</th>
                                <th>Batch #</th>
                                <th>Category</th>
                                <th>Supplier</th>
                                <th>Stock</th>
                                <th>Expiry Date</th>
                                <th>Days Left</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($medicines as $medicine)
                            @php
                                $daysLeft = $medicine->expiry_date->diffInDays(now());
                                $status = $daysLeft < 0 ? 'expired' : ($daysLeft <= 30 ? 'critical' : ($daysLeft <= 90 ? 'warning' : 'good'));
                            @endphp
                            <tr>
                                <td>
                                    <strong>{{ $medicine->name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $medicine->generic_name }}</small>
                                </td>
                                <td>{{ $medicine->batch_number ?? 'N/A' }}</td>
                                <td>{{ $medicine->category->name }}</td>
                                <td>{{ $medicine->supplier->name ?? 'N/A' }}</td>
                                <td>{{ $medicine->quantity }}</td>
                                <td>
                                    <strong>{{ $medicine->expiry_date->format('M d, Y') }}</strong>
                                </td>
                                <td>
                                    @if($daysLeft < 0)
                                        <span class="badge bg-dark">{{ abs($daysLeft) }} days expired</span>
                                    @else
                                        <span class="badge bg-{{ $daysLeft <= 30 ? 'danger' : ($daysLeft <= 90 ? 'warning' : 'success') }}">
                                            {{ $daysLeft }} days
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($daysLeft < 0)
                                        <span class="badge bg-dark">Expired</span>
                                    @elseif($daysLeft <= 30)
                                        <span class="badge bg-danger">Critical</span>
                                    @elseif($daysLeft <= 90)
                                        <span class="badge bg-warning">Expiring Soon</span>
                                    @else
                                        <span class="badge bg-success">Good</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('medicines.show', $medicine) }}" 
                                       class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('medicines.edit', $medicine) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
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
                            Showing <strong>{{ $medicines->firstItem() }}</strong> to 
                            <strong>{{ $medicines->lastItem() }}</strong> of 
                            <strong>{{ $medicines->total() }}</strong> entries
                        </p>
                    </div>
                    <div>
                        {{ $medicines->withQueryString()->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
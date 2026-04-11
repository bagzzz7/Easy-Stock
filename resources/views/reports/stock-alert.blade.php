@extends('layouts.app')

@section('title', 'Stock Alert Report')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="fw-bold">
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                    Stock Alert Report
                </h2>
                
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h6 class="text-dark-50">Total Low Stock Items</h6>
                    <h3 class="mb-0">{{ $totalLowStock }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h6 class="text-white-50">Critical Stock (≤ 5)</h6>
                    <h3 class="mb-0">{{ $criticalStock }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-dark text-white">
                <div class="card-body">
                    <h6 class="text-white-50">Out of Stock</h6>
                    <h3 class="mb-0">{{ $outOfStock }}</h3>
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
            <form action="{{ route('reports.stock-alert') }}" method="GET" class="row g-3">
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
                    <label class="form-label">Supplier</label>
                    <select name="supplier_id" class="form-select">
                        <option value="">All Suppliers</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Stock Status</label>
                    <select name="stock_status" class="form-select">
                        <option value="">All Low Stock</option>
                        <option value="critical" {{ request('stock_status') == 'critical' ? 'selected' : '' }}>Critical (≤ 5)</option>
                        <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Low (6-10)</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                @if(request()->hasAny(['category_id', 'supplier_id', 'stock_status']))
                <div class="col-12 text-end">
                    <a href="{{ route('reports.stock-alert') }}" class="btn btn-link text-danger">
                        <i class="fas fa-times-circle me-1"></i>Clear Filters
                    </a>
                </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Stock Alert Table -->
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Low Stock Medicines</h5>
        </div>
        <div class="card-body">
            @if($medicines->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                    <h5 class="text-muted">No low stock items found!</h5>
                    <p class="text-muted">All medicines are sufficiently stocked.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Medicine</th>
                                <th>Generic Name</th>
                                <th>Category</th>
                                <th>Supplier</th>
                                <th>Current Stock</th>
                                <th>Reorder Level</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($medicines as $medicine)
                            <tr>
                                <td>
                                    <strong>{{ $medicine->name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $medicine->brand }}</small>
                                </td>
                                <td>{{ $medicine->generic_name }}</td>
                                <td>{{ $medicine->category->name }}</td>
                                <td>{{ $medicine->supplier->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ $medicine->quantity <= 5 ? 'danger' : 'warning' }} fs-6">
                                        {{ $medicine->quantity }}
                                    </span>
                                </td>
                                <td>{{ $medicine->reorder_level }}</td>
                                <td>
                                    @if($medicine->quantity <= 0)
                                        <span class="badge bg-dark">Out of Stock</span>
                                    @elseif($medicine->quantity <= 5)
                                        <span class="badge bg-danger">Critical</span>
                                    @else
                                        <span class="badge bg-warning">Low</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('medicines.show', $medicine) }}" 
                                           class="btn btn-sm btn-outline-info" 
                                           title="View Details"
                                           data-bs-toggle="tooltip">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('medicines.edit', $medicine) }}" 
                                           class="btn btn-sm btn-outline-primary" 
                                           title="Edit Medicine"
                                           data-bs-toggle="tooltip">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('medicines.restock', $medicine) }}" 
                                           class="btn btn-sm btn-outline-success" 
                                           title="Restock Medicine"
                                           data-bs-toggle="tooltip">
                                            <i class="fas fa-plus-circle"></i>
                                        </a>
                                    </div>
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
                        {{ $medicines->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Enable tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush
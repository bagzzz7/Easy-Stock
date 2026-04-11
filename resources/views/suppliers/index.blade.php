@extends('layouts.app')

@section('title', 'Suppliers')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="fw-bold">
                    <i class="fas fa-truck text-primary me-2"></i>
                    Suppliers
                </h2>
                <div>
                    <a href="{{ route('suppliers.create') }}" class="btn btn-success">
                        <i class="fas fa-plus-circle me-2"></i>Add Supplier
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('suppliers.index') }}" method="GET" class="row g-3">
                <div class="col-md-10">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-search text-primary"></i>
                        </span>
                        <input type="text" 
                               name="search" 
                               class="form-control border-start-0" 
                               placeholder="Search by name, email, phone, or contact person..."
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-2"></i>Search
                    </button>
                </div>
                @if(request()->has('search'))
                <div class="col-12 text-end">
                    <a href="{{ route('suppliers.index') }}" class="btn btn-link text-danger">
                        <i class="fas fa-times-circle me-1"></i>Clear Search
                    </a>
                </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Suppliers Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            @if($suppliers->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-truck fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">No suppliers found</h5>
                    <p class="text-muted">Start by adding a new supplier.</p>
                    <a href="{{ route('suppliers.create') }}" class="btn btn-success mt-3">
                        <i class="fas fa-plus-circle me-2"></i>Add Supplier
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Company Name</th>
                                <th>Contact Person</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Address</th>
                                <th>Products</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($suppliers as $supplier)
                            <tr>
                                <td>
                                    <strong>{{ $supplier->name }}</strong>
                                </td>
                                <td>{{ $supplier->contact_person ?? '—' }}</td>
                                <td>
                                    <a href="mailto:{{ $supplier->email }}" class="text-decoration-none">
                                        <i class="fas fa-envelope text-muted me-1"></i>
                                        {{ $supplier->email }}
                                    </a>
                                </td>
                                <td>
                                    <a href="tel:{{ $supplier->phone }}" class="text-decoration-none">
                                        <i class="fas fa-phone text-muted me-1"></i>
                                        {{ $supplier->phone }}
                                    </a>
                                </td>
                                <td>
                                    <small>{{ Str::limit($supplier->address, 30) }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $supplier->medicines_count }}</span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('suppliers.show', $supplier) }}" 
                                           class="btn btn-sm btn-outline-info" 
                                           title="View Details"
                                           data-bs-toggle="tooltip">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('suppliers.edit', $supplier) }}" 
                                           class="btn btn-sm btn-outline-primary" 
                                           title="Edit"
                                           data-bs-toggle="tooltip">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($supplier->medicines_count == 0)
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger" 
                                                title="Delete"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteModal{{ $supplier->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        @else
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-secondary" 
                                                title="Cannot delete (has products)"
                                                disabled
                                                data-bs-toggle="tooltip">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        @endif       
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
                            Showing <strong>{{ $suppliers->firstItem() }}</strong> to 
                            <strong>{{ $suppliers->lastItem() }}</strong> of 
                            <strong>{{ $suppliers->total() }}</strong> entries
                        </p>
                    </div>
                    <div>
                        {{ $suppliers->withQueryString()->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Modals -->
@foreach($suppliers as $supplier)
@if($supplier->medicines_count == 0)
<div class="modal fade" id="deleteModal{{ $supplier->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Delete Supplier
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete supplier <strong>{{ $supplier->name }}</strong>?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle me-2"></i>
                    This action cannot be undone.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>Delete Supplier
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endforeach

@push('scripts')
<script>
    // Enable tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });

    // Toggle status function
    function toggleStatus(supplierId) {
        if (confirm('Are you sure you want to change this supplier\'s status?')) {
            document.getElementById('toggle-status-form-' + supplierId).submit();
        }
    }
</script>
@endpush
@endsection
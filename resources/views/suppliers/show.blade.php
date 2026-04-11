@extends('layouts.app')

@section('title', 'Supplier Details - ' . $supplier->name)

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="fw-bold">
                    <i class="fas fa-truck text-primary me-2"></i>
                    Supplier Details
                </h2>
                <div>
                    <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-primary me-2">
                        <i class="fas fa-edit me-2"></i>Edit
                    </a>
                    <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Supplier Information -->
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Company Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="bg-light rounded-circle d-inline-flex p-4 mb-3">
                            <i class="fas fa-building fa-4x text-primary"></i>
                        </div>
                        <h4 class="fw-bold">{{ $supplier->name }}</h4>
                    </div>

                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="fw-bold" width="140">Contact Person:</td>
                            <td>{{ $supplier->contact_person ?? '—' }}</td>
                        </tr>
                        @if($supplier->license_number)
                        <tr>
                            <td class="fw-bold">License Number:</td>
                            <td>
                                <span class="badge bg-secondary">
                                    <i class="fas fa-id-card"></i> {{ $supplier->license_number }}
                                </span>
                            </td>
                        </tr>
                        @endif
                        <tr>
                            <td class="fw-bold">Email:</td>
                            <td>
                                <a href="mailto:{{ $supplier->email }}" class="text-decoration-none">
                                    <i class="fas fa-envelope me-1"></i>{{ $supplier->email }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Phone:</td>
                            <td>
                                <a href="tel:{{ $supplier->phone }}" class="text-decoration-none">
                                    <i class="fas fa-phone me-1"></i>{{ $supplier->phone }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Address:</td>
                            <td>{{ $supplier->address }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Total Products:</td>
                            <td>
                                <span class="badge bg-info">{{ $supplier->medicines_count }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Joined:</td>
                            <td>{{ $supplier->created_at->format('M d, Y') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Last Updated:</td>
                            <td>{{ $supplier->updated_at->format('M d, Y') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Medicines from this supplier -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-pills me-2"></i>
                        Products from {{ $supplier->name }}
                    </h5>
                </div>
                <div class="card-body">
                    @if($supplier->medicines->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">No products yet</h5>
                            <p class="text-muted">This supplier hasn't provided any medicines yet.</p>
                            <a href="{{ route('medicines.create') }}" class="btn btn-primary mt-3">
                                <i class="fas fa-plus-circle me-2"></i>Add Medicine
                            </a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Medicine</th>
                                        <th>Generic Name</th>
                                        <th>Category</th>
                                        <th>Stock</th>
                                        <th>Price</th>
                                        <th>Expiry Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($supplier->medicines as $medicine)
                                    <tr>
                                        <td>
                                            <strong>{{ $medicine->name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $medicine->brand }}</small>
                                        </td>
                                        <td>{{ $medicine->generic_name }}</td>
                                        <td>{{ $medicine->category->name }}</td>
                                        <td>
                                            @if($medicine->quantity <= $medicine->reorder_level)
                                                <span class="badge bg-warning">{{ $medicine->quantity }}</span>
                                            @else
                                                <span class="badge bg-success">{{ $medicine->quantity }}</span>
                                            @endif
                                        </td>
                                        <td>₱{{ number_format($medicine->selling_price, 2) }}</td>
                                        <td>
                                            @php
                                                $daysUntilExpiry = $medicine->expiry_date->diffInDays(now());
                                            @endphp
                                            <span class="badge bg-{{ $daysUntilExpiry < 30 ? 'danger' : ($daysUntilExpiry < 90 ? 'warning' : 'success') }}">
                                                {{ $medicine->expiry_date->format('M d, Y') }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('medicines.show', $medicine) }}" 
                                               class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('medicines.index', ['supplier' => $supplier->id]) }}" 
                               class="btn btn-outline-primary">
                                View All Products
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
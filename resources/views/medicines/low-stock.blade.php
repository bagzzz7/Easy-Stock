@extends('layouts.app')

@section('title', 'Low Stock Medicines')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="fw-bold">
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                    Low Stock Medicines
                </h2>
                <a href="{{ route('medicines.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Medicines
                </a>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            @if($medicines->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                    <h5 class="text-muted">No low stock medicines found!</h5>
                    <p class="text-muted">All medicines are sufficiently stocked.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Medicine</th>
                                <th>Category</th>
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
                                    <small class="text-muted">{{ $medicine->generic_name }}</small>
                                </td>
                                <td>{{ $medicine->category->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-danger">{{ $medicine->quantity }}</span>
                                </td>
                                <td>{{ $medicine->reorder_level }}</td>
                                <td>
                                    <span class="badge bg-warning">Low Stock</span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('medicines.show', $medicine) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('medicines.edit', $medicine) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-success" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#restockModal{{ $medicine->id }}">
                                            <i class="fas fa-plus-circle"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{ $medicines->links() }}
            @endif
        </div>
    </div>
</div>
@endsection
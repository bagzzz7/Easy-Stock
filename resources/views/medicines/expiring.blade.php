@extends('layouts.app')

@section('title', 'Expiring Medicines')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="fw-bold">
                    <i class="fas fa-clock text-danger me-2"></i>
                    Expiring Medicines (Next 30 Days)
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
                    <h5 class="text-muted">No medicines expiring soon!</h5>
                    <p class="text-muted">All medicines have valid expiry dates.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Medicine</th>
                                <th>Batch</th>
                                <th>Expiry Date</th>
                                <th>Days Left</th>
                                <th>Stock</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($medicines as $medicine)
                            @php
                                $daysLeft = $medicine->daysUntilExpiry();
                            @endphp
                            <tr>
                                <td>
                                    <strong>{{ $medicine->name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $medicine->brand }}</small>
                                </td>
                                <td><code>{{ $medicine->batch_number }}</code></td>
                                <td>{{ $medicine->expiry_date->format('M d, Y') }}</td>
                                <td>
                                    <span class="badge bg-{{ $daysLeft < 7 ? 'danger' : ($daysLeft < 15 ? 'warning' : 'info') }}">
                                        {{ $daysLeft }} days
                                    </span>
                                </td>
                                <td>{{ $medicine->quantity }} units</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('medicines.show', $medicine) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('medicines.edit', $medicine) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
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
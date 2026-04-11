@extends('layouts.app')

@section('title', 'User Details - ' . $user->name)

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="fw-bold">
                    <i class="fas fa-user-circle text-primary me-2"></i>
                    User Details
                </h2>
                <div>
                    <a href="{{ route('users.edit', $user) }}" class="btn btn-primary me-2">
                        <i class="fas fa-edit me-2"></i>Edit
                    </a>
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- User Information -->
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Profile Information
                    </h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-4">
                        <img src="{{ $user->profile_photo_url }}" 
                             alt="{{ $user->name }}" 
                             class="rounded-circle img-thumbnail"
                             width="120" 
                             height="120"
                             style="object-fit: cover;">
                    </div>
                    
                    <h4 class="fw-bold">{{ $user->name }}</h4>
                    <p class="text-muted mb-2">
                        <span class="badge {{ $user->role_badge_class }} fs-6">
                            {{ $user->role_display_name }}
                        </span>
                    </p>
                    
                    <div class="mt-3">
                        @if($user->is_active)
                            <span class="badge bg-success">Active Account</span>
                        @else
                            <span class="badge bg-danger">Inactive Account</span>
                        @endif
                    </div>

                    <hr>

                    <table class="table table-sm table-borderless text-start">
                        <tr>
                            <td class="fw-bold" width="100">Email:</td>
                            <td>
                                <a href="mailto:{{ $user->email }}" class="text-decoration-none">
                                    <i class="fas fa-envelope me-1"></i>{{ $user->email }}
                                </a>
                            </td>
                        </tr>
                        @if($user->phone)
                        <tr>
                            <td class="fw-bold">Phone:</td>
                            <td>
                                <a href="tel:{{ $user->phone }}" class="text-decoration-none">
                                    <i class="fas fa-phone me-1"></i>{{ $user->phone }}
                                </a>
                            </td>
                        </tr>
                        @endif
                        @if($user->address)
                        <tr>
                            <td class="fw-bold">Address:</td>
                            <td>
                                <i class="fas fa-map-marker-alt me-1"></i>{{ $user->address }}
                            </td>
                        </tr>
                        @endif
                        <tr>
                            <td class="fw-bold">Joined:</td>
                            <td>{{ $user->created_at->format('M d, Y') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Last Login:</td>
                            <td>
                                @if($user->last_login_at)
                                    {{ $user->last_login_at->format('M d, Y h:i A') }}
                                    <br>
                                    <small class="text-muted">{{ $user->last_login_at->diffForHumans() }}</small>
                                @else
                                    <span class="text-muted">Never</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sales History -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-shopping-cart me-2"></i>
                        Recent Sales by {{ $user->name }}
                    </h5>
                </div>
                <div class="card-body">
                    @if($user->sales->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">No sales yet</h5>
                            <p class="text-muted">This user hasn't processed any sales.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Invoice #</th>
                                        <th>Date & Time</th>
                                        <th>Items</th>
                                        <th>Total</th>
                                        <th>Payment</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($user->sales as $sale)
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
                                        <td>
                                            <span class="badge bg-info">{{ $sale->items->count() }}</span>
                                        </td>
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
                                               class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('sales.invoice', $sale) }}" 
                                               target="_blank"
                                               class="btn btn-sm btn-outline-success">
                                                <i class="fas fa-print"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('sales.index', ['user_id' => $user->id]) }}" 
                               class="btn btn-outline-primary">
                                View All Sales
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Account Statistics -->
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h6 class="text-white-50">Total Sales</h6>
                            <h3 class="mb-0">{{ $user->sales->count() }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h6 class="text-white-50">Revenue Generated</h6>
                            <h3 class="mb-0">₱{{ number_format($user->sales->sum('grand_total'), 2) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h6 class="text-white-50">Items Sold</h6>
                            <h3 class="mb-0">{{ $user->sales->sum(function($sale) { return $sale->items->sum('quantity'); }) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
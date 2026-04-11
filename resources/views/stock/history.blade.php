@extends('layouts.app')

@section('content')
<div class="container py-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">Stock Movement History</h2>
            <p class="text-muted mb-0">Track all inventory in and out movements</p>
        </div>
    </div>

    {{-- STATS CARDS --}}
    <div class="row mb-4">

        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-muted">Total Movements</h6>
                    <h3 class="fw-bold">{{ $totalMovements }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-muted">Today Stock In</h6>
                    <h3 class="fw-bold text-success">{{ $todayIn }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-muted">Today Stock Out</h6>
                    <h3 class="fw-bold text-danger">{{ $todayOut }}</h3>
                </div>
            </div>
        </div>

    </div>

    {{-- TABLE CARD --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white">
            <h5 class="mb-0">Recent Movements</h5>
        </div>

        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">
                        <tr>
                            <th>Medicine</th>
                            <th>Type</th>
                            <th>Quantity</th>
                            <th>Date</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($movements as $move)
                            <tr>
                                <td class="fw-semibold">
                                    {{ $move->medicine->name ?? 'N/A' }}
                                </td>

                                <td>
                                    @if($move->type == 'stock_in')
                                        <span class="badge bg-success">Stock In</span>
                                    @else
                                        <span class="badge bg-danger">Stock Out</span>
                                    @endif
                                </td>

                                <td>
                                    <span class="fw-bold">
                                        {{ $move->quantity }}
                                    </span>
                                </td>

                                <td class="text-muted">
                                    {{ $move->created_at->format('M d, Y - h:i A') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    No stock movements found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>

        {{-- PAGINATION --}}
        <div class="card-footer bg-white">
            {{ $movements->links() }}
        </div>
    </div>

</div>
@endsection
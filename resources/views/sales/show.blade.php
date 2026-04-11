@extends('layouts.app')

@section('title', 'Sale Details - #' . $sale->invoice_number)

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="fw-bold">
                    <i class="fas fa-receipt text-primary me-2"></i>
                    Sale Details
                </h2>
                <div>
                    <a href="{{ route('sales.invoice', $sale) }}" target="_blank" class="btn btn-success me-2">
                        <i class="fas fa-print me-2"></i>Print Invoice
                    </a>
                    <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Sale Information -->
    <div class="row">
        <div class="col-md-8">
            <!-- Items Table -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-shopping-cart me-2"></i>
                        Sale Items
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Medicine</th>
                                    <th>Generic Name</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sale->items as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $item->medicine->name }}</strong>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $item->medicine->generic_name ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $item->quantity }}</span>
                                    </td>
                                    <td>₱{{ number_format($item->unit_price, 2) }}</td>
                                    <td class="fw-bold">₱{{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="5" class="text-end fw-bold">Subtotal:</td>
                                    <td class="fw-bold">₱{{ number_format($sale->total_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-end">Discount:</td>
                                    <td class="text-danger">
                                        @if($sale->discount > 0)
                                            -₱{{ number_format($sale->discount, 2) }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-end">Tax:</td>
                                    <td>₱{{ number_format($sale->tax, 2) }}</td>
                                </tr>
                                <tr class="table-active">
                                    <td colspan="5" class="text-end fw-bold fs-5">Grand Total:</td>
                                    <td class="fw-bold fs-5 text-primary">₱{{ number_format($sale->grand_total, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Transaction Summary -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Transaction Summary
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="fw-bold">Invoice Number:</td>
                            <td class="text-end">
                                <span class="badge bg-secondary">#{{ $sale->invoice_number }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Date & Time:</td>
                            <td class="text-end">
                                {{ $sale->created_at->format('M d, Y h:i A') }}
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Cashier:</td>
                            <td class="text-end">{{ $sale->user->name }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Payment Method:</td>
                            <td class="text-end">
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
                        </tr>
                        @if($sale->notes)
                        <tr>
                            <td class="fw-bold" colspan="2">Notes:</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-muted small p-2 bg-light rounded">
                                {{ $sale->notes }}
                            </td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-cog me-2"></i>
                        Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('sales.invoice', $sale) }}" target="_blank" class="btn btn-outline-success">
                            <i class="fas fa-print me-2"></i>Print Invoice
                        </a>
                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="fas fa-trash-alt me-2"></i>Delete Sale
                        </button>
                        <a href="{{ route('sales.create') }}" class="btn btn-outline-primary">
                            <i class="fas fa-plus-circle me-2"></i>New Sale
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Delete Sale
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this sale?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Invoice #{{ $sale->invoice_number }}</strong><br>
                    <small>This action will restore all medicine stocks and cannot be undone.</small>
                </div>
                <table class="table table-sm table-bordered">
                    <tr>
                        <th>Date:</th>
                        <td>{{ $sale->created_at->format('M d, Y h:i A') }}</td>
                    </tr>
                    <tr>
                        <th>Total Items:</th>
                        <td>{{ $sale->items->count() }}</td>
                    </tr>
                    <tr>
                        <th>Grand Total:</th>
                        <td class="fw-bold text-primary">₱{{ number_format($sale->grand_total, 2) }}</td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('sales.destroy', $sale) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>Delete Permanently
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        document.querySelectorAll('.alert').forEach(function(alert) {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);
</script>
@endpush
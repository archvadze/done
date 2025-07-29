@extends('layouts.app')

@section('title', 'Payment History')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-history me-2"></i>
                        Payment History
                    </h4>
                    <a href="{{ route('payments.show') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i>
                        Make Payment
                    </a>
                </div>

                <div class="card-body">
                    @if($payments->count() > 0)
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h6 class="card-title text-muted">Current Balance</h6>
                                        <h3 class="text-success mb-0">
                                            ${{ number_format(auth()->user()->balance ?? 0, 2) }}
                                        </h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h6 class="card-title text-muted">Total Payments</h6>
                                        <h3 class="text-primary mb-0">
                                            ${{ number_format($payments->sum('amount'), 2) }}
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Provider</th>
                                        <th>Payment ID</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payments as $payment)
                                        <tr>
                                            <td>
                                                <div class="fw-medium">
                                                    {{ $payment->created_at->format('M j, Y') }}
                                                </div>
                                                <small class="text-muted">
                                                    {{ $payment->created_at->format('g:i A') }}
                                                </small>
                                            </td>
                                            <td>
                                                <span class="fw-bold">
                                                    {{ $payment->formatted_amount }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($payment->metadata && isset($payment->metadata['type']))
                                                    <span class="badge bg-info">
                                                        {{ ucfirst($payment->metadata['type']) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @switch($payment->status)
                                                    @case('completed')
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-check me-1"></i>
                                                            Completed
                                                        </span>
                                                        @break
                                                    @case('pending')
                                                        <span class="badge bg-warning">
                                                            <i class="fas fa-clock me-1"></i>
                                                            Pending
                                                        </span>
                                                        @break
                                                    @case('failed')
                                                        <span class="badge bg-danger">
                                                            <i class="fas fa-times me-1"></i>
                                                            Failed
                                                        </span>
                                                        @break
                                                    @case('refunded')
                                                        <span class="badge bg-secondary">
                                                            <i class="fas fa-undo me-1"></i>
                                                            Refunded
                                                        </span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-light text-dark">
                                                            {{ ucfirst($payment->status) }}
                                                        </span>
                                                @endswitch
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($payment->provider === 'stripe')
                                                        <i class="fab fa-stripe text-primary me-2"></i>
                                                        Stripe
                                                    @else
                                                        <i class="fas fa-credit-card text-muted me-2"></i>
                                                        {{ ucfirst($payment->provider) }}
                                                    @endif
                                                    
                                                    @if($payment->metadata && isset($payment->metadata['mock']) && $payment->metadata['mock'])
                                                        <span class="badge bg-info ms-2" title="Test payment">
                                                            <i class="fas fa-flask"></i>
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if($payment->payment_id)
                                                    <code class="small">
                                                        {{ Str::limit($payment->payment_id, 20) }}
                                                    </code>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    @if($payment->isCompleted())
                                                        <button class="btn btn-sm btn-outline-primary" 
                                                                title="Download Receipt"
                                                                onclick="downloadReceipt('{{ $payment->id }}')">
                                                            <i class="fas fa-download"></i>
                                                        </button>
                                                    @endif
                                                    
                                                    <button class="btn btn-sm btn-outline-secondary" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#paymentModal{{ $payment->id }}"
                                                            title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $payments->links() }}
                        </div>

                        <!-- Payment Detail Modals -->
                        @foreach($payments as $payment)
                            <div class="modal fade" id="paymentModal{{ $payment->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">
                                                Payment Details
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <dl class="row">
                                                <dt class="col-sm-4">Payment ID:</dt>
                                                <dd class="col-sm-8">
                                                    <code>{{ $payment->id }}</code>
                                                </dd>

                                                <dt class="col-sm-4">Amount:</dt>
                                                <dd class="col-sm-8">{{ $payment->formatted_amount }}</dd>

                                                <dt class="col-sm-4">Status:</dt>
                                                <dd class="col-sm-8">
                                                    <span class="badge bg-{{ $payment->isCompleted() ? 'success' : ($payment->isPending() ? 'warning' : 'danger') }}">
                                                        {{ ucfirst($payment->status) }}
                                                    </span>
                                                </dd>

                                                <dt class="col-sm-4">Provider:</dt>
                                                <dd class="col-sm-8">{{ ucfirst($payment->provider) }}</dd>

                                                <dt class="col-sm-4">Date:</dt>
                                                <dd class="col-sm-8">
                                                    {{ $payment->created_at->format('F j, Y \a\t g:i A') }}
                                                </dd>

                                                @if($payment->payment_id)
                                                    <dt class="col-sm-4">External ID:</dt>
                                                    <dd class="col-sm-8">
                                                        <code>{{ $payment->payment_id }}</code>
                                                    </dd>
                                                @endif

                                                @if($payment->session_id)
                                                    <dt class="col-sm-4">Session ID:</dt>
                                                    <dd class="col-sm-8">
                                                        <code>{{ $payment->session_id }}</code>
                                                    </dd>
                                                @endif

                                                @if($payment->metadata)
                                                    <dt class="col-sm-4">Metadata:</dt>
                                                    <dd class="col-sm-8">
                                                        <pre class="small text-muted">{{ json_encode($payment->metadata, JSON_PRETTY_PRINT) }}</pre>
                                                    </dd>
                                                @endif
                                            </dl>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                Close
                                            </button>
                                            @if($payment->isCompleted())
                                                <button type="button" class="btn btn-primary" 
                                                        onclick="downloadReceipt('{{ $payment->id }}')">
                                                    <i class="fas fa-download me-1"></i>
                                                    Download Receipt
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No payments found</h5>
                            <p class="text-muted mb-4">
                                You haven't made any payments yet. Get started by making your first payment.
                            </p>
                            <a href="{{ route('payments.show') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>
                                Make Your First Payment
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function downloadReceipt(paymentId) {
    // This would typically generate and download a PDF receipt
    alert('Receipt download functionality would be implemented here for payment ID: ' + paymentId);
    // Example: window.location.href = '/payments/' + paymentId + '/receipt';
}
</script>
@endsection

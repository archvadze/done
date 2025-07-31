@extends('layouts.app')

@section('title', __('Payment History'))

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">{{ __('Payment History') }}</h1>
                <p class="text-gray-600 dark:text-gray-400">{{ __('View all your past transactions and payments') }}</p>
            </div>

            @if ($payments->count() > 0)
                <!-- Payments Table -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ __('Date') }}
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ __('Transaction ID') }}
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ __('Amount') }}
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ __('Type') }}
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ __('Status') }}
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ __('Method') }}
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ __('Actions') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($payments as $payment)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ $payment->created_at->format('M j, Y') }}
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $payment->created_at->format('g:i A') }}
                                            </div>
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-500 dark:text-gray-400">
                                            {{ $payment->transaction_id ?? $payment->id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            <div class="font-medium">
                                                {{ number_format($payment->amount, 2) }}
                                                {{ strtoupper($payment->currency) }}
                                            </div>
                                            @if ($payment->fee_amount && $payment->fee_amount > 0)
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ __('Fee') }}: {{ number_format($payment->fee_amount, 2) }}
                                                    {{ strtoupper($payment->currency) }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            <span class="capitalize">{{ $payment->type ?? 'payment' }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $statusClass = match ($payment->status) {
                                                    'completed',
                                                    'succeeded'
                                                        => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-400',
                                                    'pending'
                                                        => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-400',
                                                    'failed',
                                                    'canceled'
                                                        => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-400',
                                                    default
                                                        => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                                };
                                            @endphp
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                                @switch($payment->status)
                                                    @case('completed')
                                                    @case('succeeded')
                                                        <i class="fas fa-check-circle mr-1"></i>
                                                        {{ __('Completed') }}
                                                    @break

                                                    @case('pending')
                                                        <i class="fas fa-clock mr-1"></i>
                                                        {{ __('Pending') }}
                                                    @break

                                                    @case('failed')
                                                        <i class="fas fa-times-circle mr-1"></i>
                                                        {{ __('Failed') }}
                                                    @break

                                                    @case('canceled')
                                                        <i class="fas fa-ban mr-1"></i>
                                                        {{ __('Canceled') }}
                                                    @break

                                                    @default
                                                        {{ ucfirst($payment->status) }}
                                                @endswitch
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            @if ($payment->payment_method)
                                                <div class="flex items-center">
                                                    @switch($payment->payment_method)
                                                        @case('stripe')
                                                            <i class="fab fa-stripe text-blue-500 mr-2"></i>
                                                            {{ __('Credit Card') }}
                                                        @break

                                                        @case('paypal')
                                                            <i class="fab fa-paypal text-blue-600 mr-2"></i>
                                                            {{ __('PayPal') }}
                                                        @break

                                                        @case('crypto')
                                                            <i class="fab fa-bitcoin text-orange-500 mr-2"></i>
                                                            {{ __('Cryptocurrency') }}
                                                        @break

                                                        @case('mock')
                                                            <i class="fas fa-vial text-gray-500 mr-2"></i>
                                                            {{ __('Test Payment') }}
                                                        @break

                                                        @default
                                                            <i class="fas fa-credit-card text-gray-500 mr-2"></i>
                                                            {{ ucfirst($payment->payment_method) }}
                                                    @endswitch
                                                </div>
                                            @else
                                                <span class="text-gray-500 dark:text-gray-400">{{ __('Unknown') }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            <div class="flex items-center space-x-2">
                                                @if ($payment->receipt_url)
                                                    <a href="{{ $payment->receipt_url }}" target="_blank"
                                                        class="text-blue-500 hover:text-blue-600 dark:text-blue-400 dark:hover:text-blue-300"
                                                        title="{{ __('View Receipt') }}">
                                                        <i class="fas fa-receipt"></i>
                                                    </a>
                                                @endif
                                                @if ($payment->invoice_url)
                                                    <a href="{{ $payment->invoice_url }}" target="_blank"
                                                        class="text-green-500 hover:text-green-600 dark:text-green-400 dark:hover:text-green-300"
                                                        title="{{ __('View Invoice') }}">
                                                        <i class="fas fa-file-invoice"></i>
                                                    </a>
                                                @endif
                                                <button onclick="showPaymentDetails({{ $payment->id }})"
                                                    class="text-gray-500 hover:text-gray-600 dark:text-gray-400 dark:hover:text-gray-300"
                                                    title="{{ __('View Details') }}">
                                                    <i class="fas fa-info-circle"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $payments->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-12 text-center">
                    <div class="text-gray-400 text-6xl mb-4">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                        {{ __('No Payment History') }}
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">
                        {{ __('You haven\'t made any payments yet. When you do, they\'ll appear here.') }}
                    </p>
                    <a href="{{ route('payments.show') }}"
                        class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                        <i class="fas fa-plus mr-2"></i>{{ __('Make a Payment') }}
                    </a>
                </div>
            @endif

            <!-- Summary Cards -->
            @if ($payments->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 dark:bg-green-900">
                                <i class="fas fa-check-circle text-green-600 dark:text-green-400"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                                    {{ __('Successful Payments') }}</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                                    {{ $payments->whereIn('status', ['completed', 'succeeded'])->count() }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900">
                                <i class="fas fa-dollar-sign text-blue-600 dark:text-blue-400"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Total Spent') }}</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                                    @php
                                        $totalSpent = $payments
                                            ->whereIn('status', ['completed', 'succeeded'])
                                            ->sum('amount');
                                        $currency = $payments->first()->currency ?? 'USD';
                                    @endphp
                                    {{ number_format($totalSpent, 2) }} {{ strtoupper($currency) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900">
                                <i class="fas fa-calendar text-purple-600 dark:text-purple-400"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('This Month') }}</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                                    {{ $payments->where('created_at', '>=', now()->startOfMonth())->count() }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Payment Details Modal -->
    <div id="payment-details-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Payment Details') }}</h3>
                <button onclick="closePaymentDetails()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="payment-details-content" class="space-y-3">
                <!-- Details will be loaded here -->
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function showPaymentDetails(paymentId) {
            // For now, just show a placeholder - in a real app you'd fetch details via AJAX
            const modal = document.getElementById('payment-details-modal');
            const content = document.getElementById('payment-details-content');

            content.innerHTML = `
        <div class="text-center py-4">
            <i class="fas fa-spinner fa-spin text-gray-400 text-2xl"></i>
            <p class="text-gray-600 dark:text-gray-400 mt-2">{{ __('Loading payment details...') }}</p>
        </div>
    `;

            modal.classList.remove('hidden');
            modal.classList.add('flex');

            // Simulate loading
            setTimeout(() => {
                content.innerHTML = `
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">{{ __('Payment ID') }}:</span>
                    <span class="font-medium text-gray-900 dark:text-white">#${paymentId}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">{{ __('Description') }}:</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ __('Platform donation') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">{{ __('Processed by') }}:</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ __('Stripe') }}</span>
                </div>
            </div>
        `;
            }, 1000);
        }

        function closePaymentDetails() {
            const modal = document.getElementById('payment-details-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // Close modal when clicking outside
        document.getElementById('payment-details-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closePaymentDetails();
            }
        });
    </script>
@endpush

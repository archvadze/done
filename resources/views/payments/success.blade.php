@extends('layouts.app')

@section('title', 'Payment Successful')

@section('content')
<div class="max-w-2xl mx-auto bg-white shadow-md rounded-lg overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="h-8 w-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="ml-3">
                <h1 class="text-2xl font-bold text-gray-900">{{ __('Payment Successful!') }}</h1>
                <p class="text-gray-600">{{ __('Your payment has been processed successfully') }}</p>
            </div>
        </div>
    </div>

    <div class="p-6">
        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        <!-- Payment Details -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-3">{{ __('Payment Details') }}</h3>
            
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-gray-500">{{ __('Amount:') }}</span>
                    <span class="font-semibold">{{ $payment->formatted_amount }}</span>
                </div>
                
                <div>
                    <span class="text-gray-500">{{ __('Status:') }}</span>
                    <span class="px-2 py-1 rounded text-xs font-medium
                        @if($payment->status === 'completed') bg-green-100 text-green-800
                        @elseif($payment->status === 'pending') bg-yellow-100 text-yellow-800
                        @else bg-red-100 text-red-800
                        @endif">
                        {{ ucfirst($payment->status) }}
                    </span>
                </div>
                
                <div>
                    <span class="text-gray-500">{{ __('Provider:') }}</span>
                    <span class="font-semibold">{{ ucfirst($payment->provider) }}</span>
                </div>
                
                <div>
                    <span class="text-gray-500">{{ __('Payment ID:') }}</span>
                    <span class="font-mono text-xs">{{ $payment->payment_id }}</span>
                </div>
                
                <div>
                    <span class="text-gray-500">{{ __('Date:') }}</span>
                    <span class="font-semibold">{{ $payment->created_at->format('M d, Y H:i') }}</span>
                </div>
                
                @if($payment->metadata && isset($payment->metadata['mock']) && $payment->metadata['mock'])
                    <div class="col-span-2">
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-medium">
                            {{ __('Mock Payment (Development Mode)') }}
                        </span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Updated Balance -->
        <div class="bg-blue-50 rounded-lg p-4 mb-6">
            <h3 class="text-lg font-semibold text-blue-900">{{ __('Updated Balance') }}</h3>
            <p class="text-2xl font-bold text-blue-700">
                {{ number_format(auth()->user()->balance, 2) }} {{ auth()->user()->balance_currency }}
            </p>
        </div>

        <!-- Actions -->
        <div class="flex justify-between items-center pt-4">
            <a href="{{ route('payments.history') }}" 
               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                {{ __('View All Payments') }}
            </a>
            
            <div class="space-x-3">
                <a href="{{ route('payments.show') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    {{ __('Make Another Payment') }}
                </a>
                
                <a href="{{ route('dashboard') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                    {{ __('Return to Dashboard') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

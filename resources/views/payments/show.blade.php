@extends('layouts.app')

@section('title', 'Make Payment')

@section('content')
<div class="max-w-2xl mx-auto bg-white shadow-md rounded-lg overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('Make a Payment') }}</h1>
        <p class="text-gray-600 mt-2">{{ __('Support creators and add funds to your account') }}</p>
    </div>

    <div class="p-6">
        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                {{ session('error') }}
            </div>
        @endif

        <!-- Current Balance -->
        <div class="mb-6 p-4 bg-blue-50 rounded-lg">
            <h3 class="text-lg font-semibold text-blue-900">{{ __('Current Balance') }}</h3>
            <p class="text-2xl font-bold text-blue-700">
                {{ number_format(auth()->user()->balance ?? 0, 2) }} {{ auth()->user()->balance_currency ?? 'USD' }}
            </p>
        </div>

        <!-- Payment Form -->
        <form action="{{ route('payments.checkout') }}" method="POST" class="space-y-6">
            @csrf
            
            <div>
                <label for="amount" class="block text-sm font-medium text-gray-700">{{ __('Amount') }}</label>
                <div class="mt-1 relative rounded-md shadow-sm">
                    <input type="number" 
                           name="amount" 
                           id="amount" 
                           step="0.01" 
                           min="1" 
                           max="10000"
                           class="block w-full pr-20 border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                           placeholder="0.00"
                           value="{{ old('amount') }}"
                           required>
                    <div class="absolute inset-y-0 right-0 flex items-center">
                        <select name="currency" 
                                class="h-full py-0 pl-2 pr-7 border-transparent bg-transparent text-gray-500 sm:text-sm rounded-md focus:ring-blue-500 focus:border-blue-500">
                            <option value="USD" {{ old('currency', 'USD') == 'USD' ? 'selected' : '' }}>USD</option>
                            <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR</option>
                            <option value="GEL" {{ old('currency') == 'GEL' ? 'selected' : '' }}>GEL</option>
                        </select>
                    </div>
                </div>
                @error('amount')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="type" class="block text-sm font-medium text-gray-700">{{ __('Payment Type') }}</label>
                <select name="type" 
                        id="type" 
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        required>
                    <option value="donation" {{ old('type', 'donation') == 'donation' ? 'selected' : '' }}>
                        {{ __('Donation / Add Funds') }}
                    </option>
                    <option value="subscription" {{ old('type') == 'subscription' ? 'selected' : '' }}>
                        {{ __('Subscription') }}
                    </option>
                </select>
                @error('type')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Test Mode Notice -->
            @if (config('services.stripe.secret_key') === 'sk_test_example_key' || empty(config('services.stripe.secret_key')))
                <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">{{ __('Test Mode Active') }}</h3>
                            <p class="mt-1 text-sm text-yellow-700">
                                {{ __('This is a mock payment system for development. No real money will be charged.') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="flex justify-between items-center pt-4">
                <a href="{{ route('payments.history') }}" 
                   class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    {{ __('View Payment History') }}
                </a>
                
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    {{ __('Proceed to Payment') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

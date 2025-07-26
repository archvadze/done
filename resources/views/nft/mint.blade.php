@extends('layouts.app')

@section('title', 'Mint NFT - ' . $artwork->getTitle())

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ __('Mint as NFT') }}</h1>
                    <p class="text-gray-600 mt-1">{{ __('Create a unique digital token for your artwork') }}</p>
                </div>
                <a href="{{ route('artworks.show', $artwork) }}" 
                   class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 p-6">
            <!-- Artwork Preview -->
            <div class="space-y-4">
                <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden">
                    <img src="{{ $artwork->getFileUrl() }}" 
                         alt="{{ $artwork->getTitle() }}"
                         class="w-full h-full object-cover">
                </div>
                
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="font-semibold text-gray-900">{{ __('Artwork Details') }}</h3>
                    <div class="mt-2 space-y-1 text-sm text-gray-600">
                        <p><strong>{{ __('Title:') }}</strong> {{ $artwork->getTitle() }}</p>
                        <p><strong>{{ __('Artist:') }}</strong> {{ $artwork->user->name }}</p>
                        <p><strong>{{ __('Created:') }}</strong> {{ $artwork->created_at->format('M d, Y') }}</p>
                        <p><strong>{{ __('File Type:') }}</strong> {{ strtoupper(pathinfo($artwork->file_path, PATHINFO_EXTENSION)) }}</p>
                    </div>
                </div>
            </div>

            <!-- Minting Form -->
            <div class="space-y-6">
                @if (session('error'))
                    <div class="p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Wallet Connection Status -->
                <div class="bg-blue-50 rounded-lg p-4">
                    <h3 class="font-semibold text-blue-900 mb-2">{{ __('Wallet Status') }}</h3>
                    @if(auth()->user()->wallet_address)
                        <div class="flex items-center text-green-700">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            {{ __('Wallet Connected') }}: {{ substr(auth()->user()->wallet_address, 0, 6) }}...{{ substr(auth()->user()->wallet_address, -4) }}
                        </div>
                    @else
                        <div class="flex items-center text-orange-700">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ __('No wallet connected - will use Mock mode') }}
                        </div>
                        <button type="button" 
                                onclick="connectMockWallet()"
                                class="mt-2 text-sm bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded">
                            {{ __('Connect Mock Wallet') }}
                        </button>
                    @endif
                </div>

                <!-- Minting Form -->
                <form action="{{ route('nft.process-mint', $artwork) }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Network Selection -->
                    <div>
                        <label for="network" class="block text-sm font-medium text-gray-700">{{ __('Blockchain Network') }}</label>
                        <select name="network" id="network" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="mock" selected>{{ __('Mock Network (Development)') }}</option>
                            <option value="ethereum" disabled>{{ __('Ethereum (Coming Soon)') }}</option>
                            <option value="polygon" disabled>{{ __('Polygon (Coming Soon)') }}</option>
                            <option value="bsc" disabled>{{ __('BSC (Coming Soon)') }}</option>
                        </select>
                        @error('network')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- NFT Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">{{ __('NFT Name') }}</label>
                        <input type="text" name="name" id="name" 
                               value="{{ old('name', $artwork->getTitle()) }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- NFT Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">{{ __('NFT Description') }}</label>
                        <textarea name="description" id="description" rows="3"
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                  required>{{ old('description', $artwork->getDescription()) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Mint Price (Optional) -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="mint_price" class="block text-sm font-medium text-gray-700">{{ __('Mint Price (Optional)') }}</label>
                            <input type="number" name="mint_price" id="mint_price" 
                                   step="0.0001" min="0" max="10"
                                   value="{{ old('mint_price') }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="0.0000">
                            @error('mint_price')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="mint_currency" class="block text-sm font-medium text-gray-700">{{ __('Currency') }}</label>
                            <select name="mint_currency" id="mint_currency"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="ETH" {{ old('mint_currency', 'ETH') == 'ETH' ? 'selected' : '' }}>ETH</option>
                                <option value="MATIC" {{ old('mint_currency') == 'MATIC' ? 'selected' : '' }}>MATIC</option>
                                <option value="BNB" {{ old('mint_currency') == 'BNB' ? 'selected' : '' }}>BNB</option>
                            </select>
                            @error('mint_currency')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Info Box -->
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">{{ __('Mock NFT Minting') }}</h3>
                                <p class="mt-1 text-sm text-yellow-700">
                                    {{ __('This is a development environment. NFTs will be created in mock mode with simulated blockchain data. No real cryptocurrency will be used.') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-between items-center pt-4">
                        <a href="{{ route('artworks.show', $artwork) }}" 
                           class="text-gray-600 hover:text-gray-800 text-sm font-medium">
                            {{ __('← Back to Artwork') }}
                        </a>
                        
                        <button type="submit" 
                                class="bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white font-medium py-2 px-6 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                            {{ __('🎨 Mint NFT') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function connectMockWallet() {
    const mockAddress = '0xMOCK' + Math.random().toString(36).substring(2, 38).toUpperCase();
    
    fetch('{{ route("nft.connect-wallet") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            wallet_type: 'mock',
            wallet_address: mockAddress
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Failed to connect wallet: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to connect wallet');
    });
}
</script>
@endsection

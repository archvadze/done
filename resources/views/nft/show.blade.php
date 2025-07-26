@extends('layouts.app')

@section('title', 'NFT: ' . $nft->metadata['name'] ?? 'Untitled NFT')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $nft->metadata['name'] ?? 'Untitled NFT' }}</h1>
                    <p class="text-gray-600 mt-1">{{ __('Non-Fungible Token') }}</p>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="px-3 py-1 rounded-full text-sm font-medium
                        @if($nft->status === 'minted') bg-green-100 text-green-800
                        @elseif($nft->status === 'pending') bg-yellow-100 text-yellow-800
                        @else bg-red-100 text-red-800
                        @endif">
                        {{ ucfirst($nft->status) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 p-6">
            <!-- NFT Artwork -->
            <div class="space-y-4">
                <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden">
                    <img src="{{ $nft->artwork->getFileUrl() }}" 
                         alt="{{ $nft->metadata['name'] ?? 'NFT Artwork' }}"
                         class="w-full h-full object-cover">
                </div>
                
                <!-- Quick Actions -->
                <div class="flex space-x-3">
                    @if($nft->explorer_url)
                        <a href="{{ $nft->explorer_url }}" 
                           target="_blank"
                           class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-4 rounded-md text-sm font-medium">
                            {{ __('View on Explorer') }}
                        </a>
                    @endif
                    
                    @if($nft->opensea_url)
                        <a href="{{ $nft->opensea_url }}" 
                           target="_blank"
                           class="flex-1 bg-purple-600 hover:bg-purple-700 text-white text-center py-2 px-4 rounded-md text-sm font-medium">
                            {{ __('View on OpenSea') }}
                        </a>
                    @endif
                    
                    @if(!$nft->explorer_url && !$nft->opensea_url)
                        <div class="flex-1 bg-gray-100 text-gray-500 text-center py-2 px-4 rounded-md text-sm">
                            {{ __('Mock NFT - No External Links') }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- NFT Details -->
            <div class="space-y-6">
                <!-- Description -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ __('Description') }}</h3>
                    <p class="text-gray-700">{{ $nft->metadata['description'] ?? 'No description available.' }}</p>
                </div>

                <!-- Properties -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">{{ __('Properties') }}</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Token Details -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-2">{{ __('Token Details') }}</h4>
                            <div class="space-y-1 text-sm">
                                <p><span class="text-gray-600">{{ __('Token ID:') }}</span> {{ $nft->token_id }}</p>
                                <p><span class="text-gray-600">{{ __('Network:') }}</span> {{ ucfirst($nft->network) }}</p>
                                @if($nft->contract_address)
                                    <p><span class="text-gray-600">{{ __('Contract:') }}</span> 
                                       <span class="font-mono text-xs">{{ substr($nft->contract_address, 0, 8) }}...{{ substr($nft->contract_address, -6) }}</span></p>
                                @endif
                            </div>
                        </div>

                        <!-- Minting Info -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-2">{{ __('Minting Info') }}</h4>
                            <div class="space-y-1 text-sm">
                                <p><span class="text-gray-600">{{ __('Minted:') }}</span> {{ $nft->minted_at ? $nft->minted_at->format('M d, Y') : 'Pending' }}</p>
                                <p><span class="text-gray-600">{{ __('Price:') }}</span> {{ $nft->formatted_mint_price }}</p>
                                <p><span class="text-gray-600">{{ __('Creator:') }}</span> {{ $nft->user->name }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Attributes -->
                @if(isset($nft->metadata['attributes']) && is_array($nft->metadata['attributes']))
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">{{ __('Attributes') }}</h3>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach($nft->metadata['attributes'] as $attribute)
                                <div class="bg-blue-50 rounded-lg p-3 text-center">
                                    <p class="text-xs text-blue-600 font-medium uppercase tracking-wide">{{ $attribute['trait_type'] }}</p>
                                    <p class="text-sm font-semibold text-blue-900 mt-1">{{ $attribute['value'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Ownership & Transaction -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">{{ __('Ownership & Transaction') }}</h3>
                    <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">{{ __('Current Owner:') }}</span>
                            <span class="font-mono text-sm">{{ substr($nft->owner_wallet, 0, 8) }}...{{ substr($nft->owner_wallet, -6) }}</span>
                        </div>
                        
                        @if($nft->tx_hash)
                            <div class="flex justify-between">
                                <span class="text-gray-600">{{ __('Transaction:') }}</span>
                                <span class="font-mono text-sm">{{ substr($nft->tx_hash, 0, 8) }}...{{ substr($nft->tx_hash, -6) }}</span>
                            </div>
                        @endif
                        
                        @if($nft->ipfs_hash)
                            <div class="flex justify-between">
                                <span class="text-gray-600">{{ __('IPFS Hash:') }}</span>
                                <span class="font-mono text-sm">{{ substr($nft->ipfs_hash, 0, 8) }}...{{ substr($nft->ipfs_hash, -6) }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Original Artwork Link -->
                <div class="border-t pt-4">
                    <a href="{{ route('artworks.show', $nft->artwork) }}" 
                       class="inline-flex items-center text-blue-600 hover:text-blue-800">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        {{ __('View Original Artwork') }}
                    </a>
                </div>

                <!-- Owner Actions -->
                @if(auth()->id() === $nft->user_id)
                    <div class="border-t pt-4">
                        <h4 class="font-medium text-gray-900 mb-2">{{ __('Owner Actions') }}</h4>
                        <div class="space-y-2">
                            <a href="{{ route('nft.collection') }}" 
                               class="block w-full bg-gray-100 hover:bg-gray-200 text-gray-800 text-center py-2 px-4 rounded-md text-sm font-medium">
                                {{ __('View My NFT Collection') }}
                            </a>
                            
                            @if($nft->network === 'mock')
                                <div class="text-xs text-gray-500 text-center">
                                    {{ __('Transfer and selling features coming soon for real blockchain NFTs') }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

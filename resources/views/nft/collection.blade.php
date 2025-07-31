@extends('layouts.app')

@section('title', $user->name . __('\'s NFT Collection'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <img src="{{ $user->avatar ?? asset('images/default-avatar.png') }}" 
                         alt="{{ $user->name }}" 
                         class="w-16 h-16 rounded-full mr-4">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                            {{ $user->id === auth()->id() ? __('My NFT Collection') : $user->name . __('\'s NFT Collection') }}
                        </h1>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ trans_choice(':count NFT|:count NFTs', $nfts->total(), ['count' => $nfts->total()]) }}
                        </p>
                    </div>
                </div>
                
                @if($user->id === auth()->id())
                    <div class="flex space-x-3">
                        <a href="{{ route('artworks.index') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            <i class="fas fa-plus mr-2"></i>{{ __('Mint New NFT') }}
                        </a>
                        <button id="connect-wallet-btn" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            <i class="fas fa-wallet mr-2"></i>{{ __('Connect Wallet') }}
                        </button>
                    </div>
                @endif
            </div>

            <!-- Wallet Connection Status -->
            <div id="wallet-status" class="hidden bg-green-100 dark:bg-green-900 border border-green-200 dark:border-green-700 text-green-700 dark:text-green-200 px-4 py-3 rounded-lg mb-4">
                <div class="flex items-center">
                    <i class="fas fa-wallet mr-2"></i>
                    <span>{{ __('Wallet Connected') }}: <span id="wallet-address" class="font-mono text-sm"></span></span>
                    <button id="disconnect-wallet-btn" class="ml-auto text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- Stats -->
            @if($nfts->total() > 0)
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    @php
                        $totalValue = $nfts->getCollection()->sum(function($nft) {
                            return $nft->current_value ?? $nft->mint_price ?? 0;
                        });
                        $networkCounts = $nfts->getCollection()->groupBy('network')->map->count();
                        $mostValuable = $nfts->getCollection()->sortByDesc('current_value')->first();
                    @endphp
                    
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                        <div class="flex items-center">
                            <div class="p-2 rounded-full bg-blue-100 dark:bg-blue-900">
                                <i class="fas fa-coins text-blue-600 dark:text-blue-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Total Value') }}</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ number_format($totalValue, 2) }} ETH
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                        <div class="flex items-center">
                            <div class="p-2 rounded-full bg-green-100 dark:bg-green-900">
                                <i class="fas fa-gem text-green-600 dark:text-green-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Total NFTs') }}</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $nfts->total() }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                        <div class="flex items-center">
                            <div class="p-2 rounded-full bg-purple-100 dark:bg-purple-900">
                                <i class="fas fa-network-wired text-purple-600 dark:text-purple-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Networks') }}</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $networkCounts->count() }}</p>
                            </div>
                        </div>
                    </div>
                    
                    @if($mostValuable)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                            <div class="flex items-center">
                                <div class="p-2 rounded-full bg-yellow-100 dark:bg-yellow-900">
                                    <i class="fas fa-trophy text-yellow-600 dark:text-yellow-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Most Valuable') }}</p>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ number_format($mostValuable->current_value ?? $mostValuable->mint_price ?? 0, 2) }} ETH
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        @if($nfts->count() > 0)
            <!-- NFT Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($nfts as $nft)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden hover:shadow-lg transition-shadow">
                        <!-- NFT Image -->
                        <div class="aspect-square relative">
                            @if($nft->artwork && $nft->artwork->getThumbnailUrl())
                                <img src="{{ $nft->artwork->getThumbnailUrl() }}" 
                                     alt="{{ $nft->name }}" 
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                    <i class="fas fa-image text-gray-400 text-4xl"></i>
                                </div>
                            @endif
                            
                            <!-- Network Badge -->
                            <div class="absolute top-2 left-2">
                                @php
                                    $networkClass = match($nft->network) {
                                        'ethereum' => 'bg-blue-500',
                                        'polygon' => 'bg-purple-500',
                                        'bsc' => 'bg-yellow-500',
                                        'mock' => 'bg-gray-500',
                                        default => 'bg-gray-500'
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium text-white {{ $networkClass }}">
                                    {{ strtoupper($nft->network) }}
                                </span>
                            </div>

                            <!-- Rarity Badge -->
                            @if($nft->rarity)
                                <div class="absolute top-2 right-2">
                                    @php
                                        $rarityClass = match($nft->rarity) {
                                            'common' => 'bg-gray-500',
                                            'uncommon' => 'bg-green-500',
                                            'rare' => 'bg-blue-500',
                                            'epic' => 'bg-purple-500',
                                            'legendary' => 'bg-orange-500',
                                            default => 'bg-gray-500'
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium text-white {{ $rarityClass }}">
                                        {{ ucfirst($nft->rarity) }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        <!-- NFT Info -->
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                <a href="{{ route('nft.show', $nft) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                    {{ $nft->name }}
                                </a>
                            </h3>
                            
                            @if($nft->description)
                                <p class="text-gray-600 dark:text-gray-400 text-sm mb-3">
                                    {{ Str::limit($nft->description, 100) }}
                                </p>
                            @endif

                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Mint Price') }}</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $nft->mint_price ? number_format($nft->mint_price, 3) . ' ' . $nft->mint_currency : __('Free') }}
                                    </p>
                                </div>
                                @if($nft->current_value)
                                    <div class="text-right">
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Current Value') }}</p>
                                        <p class="text-sm font-medium text-green-600 dark:text-green-400">
                                            {{ number_format($nft->current_value, 3) }} ETH
                                        </p>
                                    </div>
                                @endif
                            </div>

                            <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 mb-3">
                                <span>{{ __('Token ID') }}: {{ $nft->token_id ?? 'N/A' }}</span>
                                <span>{{ $nft->minted_at ? $nft->minted_at->format('M j, Y') : __('Draft') }}</span>
                            </div>

                            <!-- Actions -->
                            <div class="flex space-x-2">
                                <a href="{{ route('nft.show', $nft) }}" 
                                   class="flex-1 bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded text-center text-sm font-medium transition-colors">
                                    {{ __('View Details') }}
                                </a>
                                @if($nft->external_url)
                                    <a href="{{ $nft->external_url }}" 
                                       target="_blank"
                                       class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-2 rounded text-sm font-medium transition-colors">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $nfts->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-12 text-center">
                <div class="text-gray-400 text-6xl mb-4">
                    <i class="fas fa-gem"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                    {{ $user->id === auth()->id() ? __('No NFTs Yet') : __('No NFTs in Collection') }}
                </h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    {{ $user->id === auth()->id() 
                        ? __('Start creating and minting your first NFT from your artworks.') 
                        : __('This user hasn\'t minted any NFTs yet.') }}
                </p>
                @if($user->id === auth()->id())
                    <div class="space-x-4">
                        <a href="{{ route('artworks.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                            <i class="fas fa-palette mr-2"></i>{{ __('View My Artworks') }}
                        </a>
                        <a href="{{ route('artworks.create') }}" class="inline-flex items-center px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                            <i class="fas fa-plus mr-2"></i>{{ __('Upload Artwork') }}
                        </a>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const connectBtn = document.getElementById('connect-wallet-btn');
    const disconnectBtn = document.getElementById('disconnect-wallet-btn');
    const walletStatus = document.getElementById('wallet-status');
    const walletAddress = document.getElementById('wallet-address');

    // Mock wallet connection
    if (connectBtn) {
        connectBtn.addEventListener('click', function() {
            // Simulate wallet connection
            const mockAddress = '0x' + Math.random().toString(16).substr(2, 40);
            walletAddress.textContent = mockAddress.substring(0, 6) + '...' + mockAddress.substring(38);
            walletStatus.classList.remove('hidden');
            connectBtn.textContent = '{{ __("Wallet Connected") }}';
            connectBtn.disabled = true;
            connectBtn.classList.add('opacity-50', 'cursor-not-allowed');
        });
    }

    if (disconnectBtn) {
        disconnectBtn.addEventListener('click', function() {
            walletStatus.classList.add('hidden');
            if (connectBtn) {
                connectBtn.textContent = '{{ __("Connect Wallet") }}';
                connectBtn.disabled = false;
                connectBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        });
    }
});
</script>
@endpush
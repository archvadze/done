@extends('layouts.app')

@section('title', ($user->id === auth()->id() ? 'My' : $user->name . '\'s') . ' NFT Collection')

@section('content')
<div class="min-h-screen bg-primary">
    <div class="max-w-6xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold text-secondary mb-2">
                        @if($user->id === auth()->id())
                            My NFT Collection
                        @else
                            {{ $user->name }}'s NFT Collection
                        @endif
                    </h1>
                    <p class="text-white">
                        {{ $nfts->total() }} NFT{{ $nfts->total() !== 1 ? 's' : '' }} owned
                    </p>
                </div>
                
                @auth
                    @if($user->id === auth()->user()->id)
                        <div class="space-x-4">
                            <a href="{{ route('artworks.index') }}" class="btn-secondary px-4 py-2">
                                Browse My Artworks
                            </a>
                            <button onclick="connectWallet()" id="connect-wallet-btn" class="btn-primary px-4 py-2">
                                {{ auth()->user()->wallet_address ? 'Wallet Connected' : 'Connect Wallet' }}
                            </button>
                        </div>
                    @endif
                @endauth
            </div>
        </div>

        <!-- Wallet Status -->
        @auth
            @if($user->id === auth()->user()->id)
                <div class="bg-secondary p-6 mb-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-white font-semibold mb-2">Wallet Status</h3>
                            @if(auth()->user()->wallet_address)
                                <p class="text-green-400 mb-1">✓ Wallet Connected</p>
                                <p class="text-gray-300 text-sm">{{ Str::limit(auth()->user()->wallet_address, 20, '...') }}</p>
                                <p class="text-gray-400 text-xs">Connected {{ auth()->user()->wallet_connected_at->diffForHumans() }}</p>
                            @else
                                <p class="text-yellow-400 mb-1">⚠ No Wallet Connected</p>
                                <p class="text-gray-300 text-sm">Connect your crypto wallet to mint and trade NFTs</p>
                            @endif
                        </div>
                        
                        @if(auth()->user()->wallet_address)
                            <button onclick="disconnectWallet()" class="text-red-400 hover:underline text-sm">
                                Disconnect
                            </button>
                        @endif
                    </div>
                </div>
            @endif
        @endauth

        <!-- Collection Grid -->
        @if($nfts->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($nfts as $nft)
                    <div class="bg-secondary group hover:bg-opacity-80 transition-colors">
                        <!-- NFT Image -->
                        <div class="aspect-square relative overflow-hidden">
                            @if($nft->artwork->file_path)
                                <img src="{{ asset('storage/' . $nft->artwork->file_path) }}" 
                                     alt="{{ $nft->artwork->getTitle() }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-full bg-primary flex items-center justify-center">
                                    <div class="text-center">
                                        <div class="text-6xl mb-2">🎨</div>
                                        <p class="text-white font-medium">{{ $nft->artwork->getTitle() }}</p>
                                    </div>
                                </div>
                            @endif
                            
                            <!-- NFT Badge -->
                            <div class="absolute top-3 left-3">
                                <span class="px-2 py-1 bg-purple-600 text-white text-xs font-bold rounded">NFT</span>
                            </div>
                            
                            <!-- Chain Badge -->
                            @if($nft->blockchain_network)
                                <div class="absolute top-3 right-3">
                                    <span class="px-2 py-1 bg-blue-600 text-white text-xs font-bold rounded">
                                        {{ strtoupper($nft->blockchain_network) }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        <!-- NFT Info -->
                        <div class="p-4">
                            <h3 class="text-white font-semibold mb-2 group-hover:text-secondary transition-colors">
                                <a href="{{ route('nft.show', $nft) }}">
                                    {{ $nft->artwork->getTitle() }}
                                </a>
                            </h3>
                            
                            <div class="space-y-2 text-sm text-gray-300">
                                <!-- Creator -->
                                <div class="flex items-center justify-between">
                                    <span>Creator:</span>
                                    <span class="text-white">{{ $nft->artwork->user->name }}</span>
                                </div>
                                
                                <!-- Minted Date -->
                                <div class="flex items-center justify-between">
                                    <span>Minted:</span>
                                    <span class="text-white">{{ $nft->minted_at->format('M j, Y') }}</span>
                                </div>
                                
                                <!-- Token ID -->
                                @if($nft->token_id)
                                    <div class="flex items-center justify-between">
                                        <span>Token ID:</span>
                                        <span class="text-white font-mono">#{{ $nft->token_id }}</span>
                                    </div>
                                @endif
                                
                                <!-- Price -->
                                @if($nft->mint_price)
                                    <div class="flex items-center justify-between">
                                        <span>Mint Price:</span>
                                        <span class="text-secondary font-semibold">
                                            {{ $nft->mint_price }} {{ $nft->currency ?? 'ETH' }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <!-- Action Buttons -->
                            <div class="mt-4 flex space-x-2">
                                <a href="{{ route('nft.show', $nft) }}" 
                                   class="flex-1 text-center py-2 bg-primary text-white hover:bg-opacity-80 transition-colors text-sm">
                                    View Details
                                </a>
                                
                                @if($nft->marketplace_url)
                                    <a href="{{ $nft->marketplace_url }}" 
                                       target="_blank"
                                       class="flex-1 text-center py-2 bg-purple-600 text-white hover:bg-purple-700 transition-colors text-sm">
                                        View on Market
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
            <div class="text-center py-16">
                <div class="text-8xl mb-6">🎨</div>
                <h2 class="text-2xl font-bold text-white mb-4">
                    @if($user->id === auth()->id())
                        No NFTs in Your Collection
                    @else
                        No NFTs in This Collection
                    @endif
                </h2>
                <p class="text-gray-300 mb-8 max-w-md mx-auto">
                    @if($user->id === auth()->id())
                        Start your NFT journey by minting your first artwork as an NFT.
                    @else
                        {{ $user->name }} hasn't minted any NFTs yet.
                    @endif
                </p>
                
                @auth
                    @if($user->id === auth()->user()->id)
                        <div class="space-x-4">
                            <a href="{{ route('artworks.index') }}" class="btn-primary px-6 py-3">
                                Browse My Artworks
                            </a>
                            <a href="{{ route('artworks.create') }}" class="btn-secondary px-6 py-3">
                                Upload New Artwork
                            </a>
                        </div>
                    @endif
                @endauth
            </div>
        @endif

        <!-- Collection Stats -->
        @if($nfts->count() > 0)
            <div class="mt-12 grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-secondary p-6 text-center">
                    <div class="text-3xl font-bold text-secondary mb-2">{{ $nfts->total() }}</div>
                    <div class="text-gray-300">Total NFTs</div>
                </div>
                
                <div class="bg-secondary p-6 text-center">
                    <div class="text-3xl font-bold text-secondary mb-2">
                        {{ $nfts->where('blockchain_network', 'ethereum')->count() }}
                    </div>
                    <div class="text-gray-300">Ethereum</div>
                </div>
                
                <div class="bg-secondary p-6 text-center">
                    <div class="text-3xl font-bold text-secondary mb-2">
                        {{ $nfts->where('blockchain_network', 'polygon')->count() }}
                    </div>
                    <div class="text-gray-300">Polygon</div>
                </div>
                
                <div class="bg-secondary p-6 text-center">
                    <div class="text-3xl font-bold text-secondary mb-2">
                        {{ $nfts->whereNotNull('marketplace_url')->count() }}
                    </div>
                    <div class="text-gray-300">Listed</div>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
async function connectWallet() {
    try {
        const response = await fetch('{{ route("nft.connect-wallet") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                wallet_address: '0x1234567890123456789012345678901234567890', // Mock address
                wallet_type: 'MetaMask'
            })
        });

        const data = await response.json();
        
        if (data.success) {
            location.reload();
        } else {
            alert('Failed to connect wallet: ' + data.message);
        }
    } catch (error) {
        alert('Error connecting wallet: ' + error.message);
    }
}

async function disconnectWallet() {
    if (!confirm('Are you sure you want to disconnect your wallet?')) {
        return;
    }

    try {
        const response = await fetch('{{ route("nft.disconnect-wallet") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        const data = await response.json();
        
        if (data.success) {
            location.reload();
        } else {
            alert('Failed to disconnect wallet: ' + data.message);
        }
    } catch (error) {
        alert('Error disconnecting wallet: ' + error.message);
    }
}
</script>
@endsection

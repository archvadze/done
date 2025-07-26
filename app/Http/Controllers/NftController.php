<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\NftOwnership;
use App\Models\CryptoPayment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NftController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show NFT minting form for an artwork
     */
    public function mint(Artwork $artwork)
    {
        // Check if user owns this artwork
        if ($artwork->user_id !== Auth::id()) {
            abort(403, 'You can only mint NFTs for your own artworks.');
        }

        // Check if artwork is already minted as NFT
        $existingNft = NftOwnership::where('artwork_id', $artwork->id)->first();
        if ($existingNft) {
            return redirect()->route('nft.show', $existingNft)
                ->with('info', 'This artwork is already minted as an NFT.');
        }

        return view('nft.mint', compact('artwork'));
    }

    /**
     * Process NFT minting
     */
    public function processMint(Request $request, Artwork $artwork)
    {
        $request->validate([
            'network' => 'required|string|in:ethereum,polygon,bsc,mock',
            'mint_price' => 'nullable|numeric|min:0|max:10',
            'mint_currency' => 'nullable|string|in:ETH,MATIC,BNB',
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
        ]);

        // Check if user owns this artwork
        if ($artwork->user_id !== Auth::id()) {
            abort(403, 'You can only mint NFTs for your own artworks.');
        }

        // Check if artwork is already minted
        $existingNft = NftOwnership::where('artwork_id', $artwork->id)->first();
        if ($existingNft) {
            return back()->with('error', 'This artwork is already minted as an NFT.');
        }

        $user = Auth::user();
        $network = $request->network;

        // Check if this is a mock minting or real blockchain
        if ($network === 'mock' || !$user->wallet_address) {
            return $this->processMockMint($request, $artwork, $user);
        }

        // For real blockchain minting (future implementation)
        return $this->processBlockchainMint($request, $artwork, $user);
    }

    /**
     * Process mock NFT minting for development
     */
    private function processMockMint(Request $request, Artwork $artwork, User $user)
    {
        $metadata = [
            'name' => $request->name,
            'description' => $request->description,
            'image' => $artwork->file_path,
            'artist' => $user->name,
            'created_date' => $artwork->created_at->toDateString(),
            'attributes' => [
                ['trait_type' => 'Artist', 'value' => $user->name],
                ['trait_type' => 'Original Platform', 'value' => 'Acumen Craft'],
                ['trait_type' => 'File Type', 'value' => pathinfo($artwork->file_path, PATHINFO_EXTENSION)],
                ['trait_type' => 'Mock NFT', 'value' => 'true'],
            ],
        ];

        // Create NFT ownership record
        $nft = NftOwnership::create([
            'artwork_id' => $artwork->id,
            'user_id' => $user->id,
            'owner_wallet' => $user->wallet_address ?? 'mock_' . $user->id,
            'network' => 'mock',
            'token_id' => 'mock_' . uniqid(),
            'contract_address' => '0xMOCK' . strtoupper(Str::random(38)),
            'tx_hash' => '0xmock' . strtoupper(Str::random(62)),
            'mint_price' => $request->mint_price ?? 0,
            'mint_currency' => $request->mint_currency ?? 'ETH',
            'metadata' => $metadata,
            'ipfs_hash' => 'QmMOCK' . Str::random(40), // Mock IPFS hash
            'status' => 'minted',
            'minted_at' => now(),
        ]);

        // Create crypto payment record if there was a mint price
        if ($request->mint_price && $request->mint_price > 0) {
            CryptoPayment::create([
                'user_id' => $user->id,
                'amount' => $request->mint_price,
                'currency' => $request->mint_currency ?? 'ETH',
                'tx_hash' => $nft->tx_hash,
                'status' => 'confirmed',
                'network' => 'mock',
                'from_address' => $user->wallet_address ?? 'mock_user_wallet',
                'to_address' => '0xMOCKMINTINGCONTRACT' . Str::random(24),
                'confirmations' => 12,
                'metadata' => [
                    'type' => 'nft_mint',
                    'artwork_id' => $artwork->id,
                    'nft_id' => $nft->id,
                    'mock' => true,
                ],
                'confirmed_at' => now(),
            ]);
        }

        return redirect()->route('nft.show', $nft)
            ->with('success', 'NFT minted successfully! (Mock Mode)');
    }

    /**
     * Process real blockchain NFT minting (placeholder for future implementation)
     */
    private function processBlockchainMint(Request $request, Artwork $artwork, User $user)
    {
        // This would integrate with real blockchain APIs like:
        // - OpenSea SDK
        // - Moralis API
        // - Alchemy NFT API
        // - Direct smart contract interaction with Web3

        return back()->with('error', 'Real blockchain minting not yet implemented. Please use Mock network for testing.');
    }

    /**
     * Show NFT details
     */
    public function show(NftOwnership $nft)
    {
        $nft->load(['artwork', 'user']);
        return view('nft.show', compact('nft'));
    }

    /**
     * Show user's NFT collection
     */
    public function collection(User $user = null)
    {
        $user = $user ?? Auth::user();
        
        $nfts = NftOwnership::with(['artwork', 'user'])
            ->where('user_id', $user->id)
            ->latest('minted_at')
            ->paginate(12);

        return view('nft.collection', compact('nfts', 'user'));
    }

    /**
     * Connect wallet (mock implementation)
     */
    public function connectWallet(Request $request)
    {
        $request->validate([
            'wallet_type' => 'required|string|in:metamask,walletconnect,coinbase,mock',
            'wallet_address' => 'required|string|min:10',
        ]);

        $user = Auth::user();
        
        // Mock wallet connection - in production this would verify wallet signature
        $user->update([
            'wallet_address' => $request->wallet_address,
            'wallet_type' => $request->wallet_type,
            'wallet_connected_at' => now(),
            'wallet_metadata' => [
                'mock' => true,
                'connected_from' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Wallet connected successfully!',
            'wallet_address' => $user->wallet_address,
        ]);
    }

    /**
     * Disconnect wallet
     */
    public function disconnectWallet()
    {
        $user = Auth::user();
        
        $user->update([
            'wallet_address' => null,
            'wallet_type' => null,
            'wallet_connected_at' => null,
            'wallet_metadata' => null,
        ]);

        return back()->with('success', 'Wallet disconnected successfully.');
    }

    /**
     * Get NFT ownership for an artwork
     */
    public function ownership(Artwork $artwork)
    {
        $nft = NftOwnership::where('artwork_id', $artwork->id)->first();
        
        if (!$nft) {
            return response()->json([
                'is_nft' => false,
                'message' => 'This artwork is not minted as an NFT.',
            ]);
        }

        return response()->json([
            'is_nft' => true,
            'nft' => [
                'id' => $nft->id,
                'token_id' => $nft->token_id,
                'network' => $nft->network,
                'status' => $nft->status,
                'owner_wallet' => $nft->owner_wallet,
                'mint_price' => $nft->formatted_mint_price,
                'explorer_url' => $nft->explorer_url,
                'opensea_url' => $nft->opensea_url,
                'minted_at' => $nft->minted_at?->toDateString(),
            ],
        ]);
    }
}

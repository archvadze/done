<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NftOwnership extends Model
{
    protected $table = 'nft_ownership';

    protected $fillable = [
        'artwork_id',
        'user_id',
        'owner_wallet',
        'network',
        'token_id',
        'contract_address',
        'tx_hash',
        'mint_price',
        'mint_currency',
        'metadata',
        'ipfs_hash',
        'status',
        'minted_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'minted_at' => 'datetime',
        'mint_price' => 'decimal:8',
    ];

    /**
     * Get the artwork that owns this NFT
     */
    public function artwork(): BelongsTo
    {
        return $this->belongsTo(Artwork::class);
    }

    /**
     * Get the user who created this NFT
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if NFT is successfully minted
     */
    public function isMinted(): bool
    {
        return $this->status === 'minted';
    }

    /**
     * Check if NFT minting is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Get blockchain explorer URL for this NFT
     */
    public function getExplorerUrlAttribute(): ?string
    {
        if (!$this->tx_hash || $this->network === 'mock') {
            return null;
        }

        $baseUrls = [
            'ethereum' => 'https://etherscan.io/tx/',
            'polygon' => 'https://polygonscan.com/tx/',
            'bsc' => 'https://bscscan.com/tx/',
        ];

        return isset($baseUrls[$this->network]) 
            ? $baseUrls[$this->network] . $this->tx_hash 
            : null;
    }

    /**
     * Get OpenSea URL for this NFT
     */
    public function getOpenSeaUrlAttribute(): ?string
    {
        if (!$this->contract_address || !$this->token_id || $this->network === 'mock') {
            return null;
        }

        $networks = [
            'ethereum' => 'ethereum',
            'polygon' => 'matic',
        ];

        $network = $networks[$this->network] ?? null;
        
        return $network 
            ? "https://opensea.io/assets/{$network}/{$this->contract_address}/{$this->token_id}"
            : null;
    }

    /**
     * Format mint price with currency
     */
    public function getFormattedMintPriceAttribute(): string
    {
        if (!$this->mint_price || !$this->mint_currency) {
            return 'Free';
        }

        return number_format($this->mint_price, 4) . ' ' . strtoupper($this->mint_currency);
    }
}

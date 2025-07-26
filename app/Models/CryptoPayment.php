<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CryptoPayment extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'currency',
        'tx_hash',
        'status',
        'network',
        'from_address',
        'to_address',
        'confirmations',
        'metadata',
        'confirmed_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'confirmed_at' => 'datetime',
        'amount' => 'decimal:8',
    ];

    /**
     * Get the user that owns the crypto payment
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if payment is confirmed
     */
    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    /**
     * Check if payment is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Get blockchain explorer URL for this transaction
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
            'bitcoin' => 'https://blockstream.info/tx/',
        ];

        return isset($baseUrls[$this->network]) 
            ? $baseUrls[$this->network] . $this->tx_hash 
            : null;
    }

    /**
     * Format amount with currency
     */
    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 6) . ' ' . strtoupper($this->currency);
    }
}

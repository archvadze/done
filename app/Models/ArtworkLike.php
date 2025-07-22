<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArtworkLike extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'artwork_id',
    ];

    /**
     * Get the user who liked the artwork
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the artwork that was liked
     */
    public function artwork(): BelongsTo
    {
        return $this->belongsTo(Artwork::class);
    }
}

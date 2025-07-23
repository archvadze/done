<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'artwork_id',
        'parent_id',
        'content',
        'content_translations',
        'is_edited',
        'edited_at',
        'status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'content_translations' => 'array',
            'is_edited' => 'boolean',
            'edited_at' => 'datetime',
        ];
    }

    /**
     * Get the user who wrote the comment
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the artwork this comment belongs to
     */
    public function artwork(): BelongsTo
    {
        return $this->belongsTo(Artwork::class);
    }

    /**
     * Get the parent comment (for replies)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * Get child comments (replies)
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id')
            ->where('status', 'active')
            ->orderBy('created_at');
    }

    /**
     * Get all replies recursively
     */
    public function allReplies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id')
            ->where('status', 'active')
            ->with('allReplies')
            ->orderBy('created_at');
    }

    /**
     * Check if comment is a reply
     */
    public function isReply(): bool
    {
        return !is_null($this->parent_id);
    }

    /**
     * Get the comment content in the specified language
     */
    public function getContentInLanguage(string $lang = 'en'): string
    {
        if ($this->content_translations && isset($this->content_translations[$lang])) {
            return $this->content_translations[$lang];
        }

        return $this->content;
    }

    /**
     * Scope for active comments only
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for root comments only (not replies)
     */
    public function scopeRootComments($query)
    {
        return $query->whereNull('parent_id');
    }
}

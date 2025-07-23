<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class Artwork extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'media_type',
        'file_path',
        'file_url',
        'thumbnail_path',
        'original_filename',
        'file_hash',
        'file_size',
        'mime_type',
        'file_metadata',
        'license_type',
        'copyright_notice',
        'watermark_enabled',
        'blockchain_timestamp',
        'blockchain_hash',
        'tags',
        'category',
        'subcategory',
        'is_ai_generated',
        'ai_tools_used',
        'visibility',
        'comments_enabled',
        'downloads_enabled',
        'is_featured',
        'view_count',
        'like_count',
        'comment_count',
        'download_count',
        'acq_score',
        'acq_breakdown',
        'evaluation_count',
        'status',
        'rejection_reason',
        'published_at',
        'archived_at',
        'is_nft',
        'nft_contract_address',
        'nft_token_id',
        'blockchain_network',
    ];

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
        'file_metadata' => 'array',
        'tags' => 'array',
        'ai_tools_used' => 'array',
        'acq_breakdown' => 'array',
        'is_ai_generated' => 'boolean',
        'watermark_enabled' => 'boolean',
        'comments_enabled' => 'boolean',
        'downloads_enabled' => 'boolean',
        'is_featured' => 'boolean',
        'is_nft' => 'boolean',
        'acq_score' => 'decimal:2',
        'file_size' => 'integer',
        'view_count' => 'integer',
        'like_count' => 'integer',
        'comment_count' => 'integer',
        'download_count' => 'integer',
        'evaluation_count' => 'integer',
        'blockchain_timestamp' => 'datetime',
        'published_at' => 'datetime',
        'archived_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ArtworkCategory::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(ArtworkLike::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)
            ->where('status', 'active')
            ->whereNull('parent_id') // Only root comments, replies are loaded separately
            ->with(['user', 'replies.user'])
            ->latest();
    }

    public function allComments(): HasMany
    {
        return $this->hasMany(Comment::class)
            ->where('status', 'active');
    }

    /**
     * Scopes
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeForSale($query)
    {
        return $query->where('is_for_sale', true);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get localized title
     */
    public function getTitle()
    {
        $locale = app()->getLocale();

        if (is_array($this->title)) {
            return $this->title[$locale] ?? $this->title['en'] ?? 'Untitled';
        }

        return $this->title ?? 'Untitled';
    }

    /**
     * Get localized description
     */
    public function getDescription()
    {
        $locale = app()->getLocale();

        if (is_array($this->description)) {
            return $this->description[$locale] ?? $this->description['en'] ?? null;
        }

        return $this->description;
    }

    /**
     * Helper methods
     */
    public function getFileUrl()
    {
        if ($this->file_path) {
            return Storage::url($this->file_path);
        }
        return null;
    }

    public function getThumbnailUrl()
    {
        if ($this->thumbnail_path) {
            return Storage::url($this->thumbnail_path);
        }
        return $this->getFileUrl();
    }

    public function getLikesCount()
    {
        return $this->likes()->count();
    }

    public function isLikedBy($user = null)
    {
        $user = $user ?: Auth::user();
        if (!$user) {
            return false;
        }

        return $this->likes()->where('user_id', $user->id)->exists();
    }

    public function incrementViewCount()
    {
        $this->increment('view_count');
    }

    public function calculateAcqScore()
    {
        // Placeholder for ACQ scoring algorithm
        // This would be implemented based on specific requirements
        $baseScore = 0;

        // Factor in likes
        $baseScore += $this->getLikesCount() * 2;

        // Factor in views
        $baseScore += $this->view_count * 0.1;

        // Factor in user reputation (if implemented)
        // $baseScore += $this->user->reputation * 0.5;

        $this->acq_score = round($baseScore, 2);
        $this->save();

        return $this->acq_score;
    }

    public function hasValidFile()
    {
        return $this->file_path && Storage::disk('public')->exists($this->file_path);
    }

    public function getFileExtension()
    {
        return pathinfo($this->file_path, PATHINFO_EXTENSION);
    }

    public function isImage()
    {
        return in_array(strtolower($this->getFileExtension()), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
    }

    public function isVideo()
    {
        return in_array(strtolower($this->getFileExtension()), ['mp4', 'webm', 'ogg', 'avi', 'mov']);
    }

    public function isAudio()
    {
        return in_array(strtolower($this->getFileExtension()), ['mp3', 'wav', 'ogg', 'flac', 'aac']);
    }

    public function getReadableFileSize()
    {
        if (!$this->file_size) {
            return 'Unknown';
        }

        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes >= 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get available license types
     */
    public static function getLicenseTypes(): array
    {
        return [
            'all_rights_reserved' => 'All Rights Reserved',
            'creative_commons_by' => 'Creative Commons BY',
            'creative_commons_by_sa' => 'Creative Commons BY-SA',
            'creative_commons_by_nc' => 'Creative Commons BY-NC',
            'creative_commons_by_nc_sa' => 'Creative Commons BY-NC-SA',
            'public_domain' => 'Public Domain',
            'nft_exclusive' => 'NFT Exclusive'
        ];
    }
}

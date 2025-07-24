<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
        'creative_process',
        'is_ai_generated',
        'ai_tools_used',
        'visibility',
        'comments_enabled',
        'downloads_enabled',
        'allow_downloads',
        'is_featured',
        'featured',
        'featured_until',
        'view_count',
        'like_count',
        'comment_count',
        'download_count',
        'acq_score',
        'acq_total_score',
        'acq_breakdown',
        'evaluation_count',
        'status',
        'rejection_reason',
        'published_at',
        'archived_at',
        'price',
        'is_for_sale',
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
        'allow_downloads' => 'boolean',
        'is_featured' => 'boolean',
        'featured' => 'boolean',
        'is_for_sale' => 'boolean',
        'is_nft' => 'boolean',
        'acq_score' => 'decimal:2',
        'acq_total_score' => 'decimal:2',
        'price' => 'decimal:2',
        'file_size' => 'integer',
        'view_count' => 'integer',
        'like_count' => 'integer',
        'comment_count' => 'integer',
        'download_count' => 'integer',
        'evaluation_count' => 'integer',
        'blockchain_timestamp' => 'datetime',
        'published_at' => 'datetime',
        'archived_at' => 'datetime',
        'featured_until' => 'datetime',
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

        $title = $this->title;

        // If title is JSON string, decode it
        if (is_string($title) && (str_starts_with($title, '{') || str_starts_with($title, '['))) {
            $title = json_decode($title, true);
        }

        if (is_array($title)) {
            return $title[$locale] ?? $title['en'] ?? 'Untitled';
        }

        return $title ?? 'Untitled';
    }

    /**
     * Get localized description
     */
    public function getDescription()
    {
        $locale = app()->getLocale();

        $description = $this->description;

        // If description is JSON string, decode it
        if (is_string($description) && (str_starts_with($description, '{') || str_starts_with($description, '['))) {
            $description = json_decode($description, true);
        }

        if (is_array($description)) {
            return $description[$locale] ?? $description['en'] ?? null;
        }

        return $description;
    }

    /**
     * Get title for a specific language
     */
    public function getTitleForLanguage($languageCode)
    {
        $title = $this->title;

        // If title is JSON string, decode it
        if (is_string($title) && (str_starts_with($title, '{') || str_starts_with($title, '['))) {
            $title = json_decode($title, true);
        }

        if (is_array($title)) {
            return $title[$languageCode] ?? '';
        }

        // If it's a string and we're asking for English, return it
        return ($languageCode === 'en') ? ($title ?? '') : '';
    }

    /**
     * Get description for a specific language
     */
    public function getDescriptionForLanguage($languageCode)
    {
        $description = $this->description;

        // If description is JSON string, decode it
        if (is_string($description) && (str_starts_with($description, '{') || str_starts_with($description, '['))) {
            $description = json_decode($description, true);
        }

        if (is_array($description)) {
            return $description[$languageCode] ?? '';
        }

        // If it's a string and we're asking for English, return it
        return ($languageCode === 'en') ? ($description ?? '') : '';
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

    /**
     * Evaluation relationship
     */
    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }

    public function incrementViewCount()
    {
        $this->increment('view_count');
    }

    public function calculateAcqScore()
    {
        // Get all approved evaluations for this artwork
        $evaluations = $this->evaluations()->where('status', 'approved')->get();

        if ($evaluations->isEmpty()) {
            $this->acq_score = null;
            $this->save();
            return $this->acq_score;
        }

        // Calculate average score from all evaluation categories
        $totalScore = 0;
        $evaluationCount = $evaluations->count();

        foreach ($evaluations as $evaluation) {
            // Average the four evaluation criteria for each evaluation
            $evaluationAverage = (
                $evaluation->score_technique +
                $evaluation->score_composition +
                $evaluation->score_originality +
                $evaluation->score_impact
            ) / 4;

            $totalScore += $evaluationAverage;
        }

        // Calculate overall average
        $acqScore = $totalScore / $evaluationCount;

        $this->acq_score = round($acqScore, 2);
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

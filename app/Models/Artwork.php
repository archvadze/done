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
        'category_id',
        'status',
        'privacy_setting',
        'file_path',
        'thumbnail_path',
        'file_type',
        'file_size',
        'metadata',
        'tags',
        'creative_process',
        'techniques_used',
        'materials_used',
        'dimensions',
        'creation_year',
        'price',
        'is_for_sale',
        'allow_downloads',
        'license_type',
        'acq_score',
        'view_count',
        'featured',
        'content_en',
        'content_ka'
    ];

    protected $casts = [
        'metadata' => 'array',
        'tags' => 'array',
        'featured' => 'boolean',
        'is_for_sale' => 'boolean',
        'allow_downloads' => 'boolean',
        'content_en' => 'array',
        'content_ka' => 'array',
        'acq_score' => 'decimal:2',
        'price' => 'decimal:2',
        'file_size' => 'integer',
        'view_count' => 'integer',
        'creation_year' => 'integer'
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
     * Accessors & Mutators
     */
    public function getTitleAttribute($value)
    {
        if (app()->getLocale() === 'ka' && !empty($this->content_ka['title'])) {
            return $this->content_ka['title'];
        }

        if (!empty($this->content_en['title'])) {
            return $this->content_en['title'];
        }

        return $value;
    }

    public function getDescriptionAttribute($value)
    {
        if (app()->getLocale() === 'ka' && !empty($this->content_ka['description'])) {
            return $this->content_ka['description'];
        }

        if (!empty($this->content_en['description'])) {
            return $this->content_en['description'];
        }

        return $value;
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
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class ArtworkCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'slug',
        'icon',
        'color_hex',
        'parent_id',
        'sort_order',
        'allowed_media_types',
        'is_active',
        'is_featured',
        'artwork_count',
    ];

    protected $casts = [
        'name' => 'array',
        'description' => 'array',
        'allowed_media_types' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
        'artwork_count' => 'integer',
    ];

    /**
     * Get the parent category
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ArtworkCategory::class, 'parent_id');
    }

    /**
     * Get child categories
     */
    public function children(): HasMany
    {
        return $this->hasMany(ArtworkCategory::class, 'parent_id')->orderBy('sort_order');
    }

    /**
     * Get artworks in this category
     */
    public function artworks(): HasMany
    {
        return $this->hasMany(Artwork::class, 'category', 'slug');
    }

    /**
     * Scope to get only active categories
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get only featured categories
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope to get root categories (no parent)
     */
    public function scopeRoots(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Get the name in the specified language
     */
    public function getName(string $locale = 'en'): string
    {
        $names = $this->name ?? [];
        return $names[$locale] ?? $names['en'] ?? 'Untitled Category';
    }

    /**
     * Get the description in the specified language
     */
    public function getDescription(string $locale = 'en'): ?string
    {
        $descriptions = $this->description ?? [];
        return $descriptions[$locale] ?? $descriptions['en'] ?? null;
    }

    /**
     * Check if a media type is allowed in this category
     */
    public function isMediaTypeAllowed(string $mediaType): bool
    {
        $allowedTypes = $this->allowed_media_types;

        // If no restrictions, all types are allowed
        if (empty($allowedTypes)) {
            return true;
        }

        return in_array($mediaType, $allowedTypes);
    }

    /**
     * Get full category path (for breadcrumbs)
     */
    public function getFullPath(string $locale = 'en', string $separator = ' > '): string
    {
        $path = [];
        $current = $this;

        while ($current) {
            array_unshift($path, $current->getName($locale));
            $current = $current->parent;
        }

        return implode($separator, $path);
    }

    /**
     * Update artwork count for this category
     */
    public function updateArtworkCount(): void
    {
        $count = $this->artworks()->published()->count();
        $this->update(['artwork_count' => $count]);
    }
}

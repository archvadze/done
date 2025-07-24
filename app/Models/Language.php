<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'native_name',
        'flag_emoji',
        'is_active',
        'is_default',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get only active languages (scope)
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->orderBy('sort_order');
    }

    /**
     * Get active languages collection
     */
    public static function getActive()
    {
        return static::active()->get();
    }

    /**
     * Get default language
     */
    public static function default()
    {
        return static::where('is_default', true)->first();
    }

    /**
     * Get supported locales array for Laravel
     */
    public static function supportedLocales()
    {
        return static::active()
            ->pluck('code')
            ->toArray();
    }

    /**
     * Check if a language code is supported and active
     */
    public static function isSupported($code)
    {
        return static::where('code', $code)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Get language by code
     */
    public static function findByCode($code)
    {
        return static::where('code', $code)->first();
    }
}

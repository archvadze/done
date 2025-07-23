<?php

namespace App\Helpers;

class LocaleHelper
{
    /**
     * Get all supported locales with their display names
     */
    public static function getSupportedLocales(): array
    {
        return [
            'en' => [
                'code' => 'en',
                'name' => 'English',
                'native' => 'English',
                'flag' => '🇺🇸'
            ],
            'ka' => [
                'code' => 'ka',
                'name' => 'Georgian',
                'native' => 'ქართული',
                'flag' => '🇬🇪'
            ],
            'de' => [
                'code' => 'de',
                'name' => 'German',
                'native' => 'Deutsch',
                'flag' => '🇩🇪'
            ]
        ];
    }

    /**
     * Get current locale information
     */
    public static function getCurrentLocale(): array
    {
        $currentLocale = app()->getLocale();
        $supportedLocales = self::getSupportedLocales();

        return $supportedLocales[$currentLocale] ?? $supportedLocales['en'];
    }

    /**
     * Get locale display name
     */
    public static function getLocaleName(string $locale): string
    {
        $supportedLocales = self::getSupportedLocales();
        return $supportedLocales[$locale]['name'] ?? $locale;
    }

    /**
     * Get locale native name
     */
    public static function getLocaleNativeName(string $locale): string
    {
        $supportedLocales = self::getSupportedLocales();
        return $supportedLocales[$locale]['native'] ?? $locale;
    }

    /**
     * Get locale flag emoji
     */
    public static function getLocaleFlag(string $locale): string
    {
        $supportedLocales = self::getSupportedLocales();
        return $supportedLocales[$locale]['flag'] ?? '🌍';
    }

    /**
     * Check if locale is supported
     */
    public static function isSupported(string $locale): bool
    {
        return array_key_exists($locale, self::getSupportedLocales());
    }

    /**
     * Get locales for select dropdown
     */
    public static function getLocalesForSelect(): array
    {
        $locales = [];
        foreach (self::getSupportedLocales() as $code => $info) {
            $locales[$code] = $info['flag'] . ' ' . $info['native'];
        }
        return $locales;
    }
}

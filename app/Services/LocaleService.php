<?php

namespace App\Services;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LocaleService
{
    /**
     * Set the application locale
     */
    public static function setLocale(string $locale): void
    {
        $supportedLocales = config('app.supported_locales', ['en']);

        if (in_array($locale, $supportedLocales)) {
            App::setLocale($locale);
            Session::put('locale', $locale);
        }
    }

    /**
     * Get the current locale
     */
    public static function getCurrentLocale(): string
    {
        return App::getLocale();
    }

    /**
     * Get the fallback locale
     */
    public static function getFallbackLocale(): string
    {
        return config('app.fallback_locale', 'en');
    }

    /**
     * Get supported locales from config
     */
    public static function getSupportedLocales(): array
    {
        return config('app.supported_locales', ['en']);
    }

    /**
     * Check if a locale is supported
     */
    public static function isSupported(string $locale): bool
    {
        return in_array($locale, self::getSupportedLocales());
    }

    /**
     * Get the best matching locale from Accept-Language header
     */
    public static function getBestMatchingLocale(array $acceptLanguages): string
    {
        $supportedLocales = self::getSupportedLocales();

        foreach ($acceptLanguages as $lang) {
            // Extract language code (e.g., 'en' from 'en-US')
            $langCode = substr($lang, 0, 2);
            if (in_array($langCode, $supportedLocales)) {
                return $langCode;
            }
        }

        return self::getFallbackLocale();
    }

    /**
     * Get localized value from multilingual array
     */
    public static function getLocalizedValue(array $translations = null, string $locale = null): string
    {
        if (!$translations) {
            return '';
        }

        $locale = $locale ?? self::getCurrentLocale();

        // Check if the requested locale exists
        if (isset($translations[$locale])) {
            return $translations[$locale];
        }

        // Fallback to default locale
        $fallbackLocale = self::getFallbackLocale();
        if (isset($translations[$fallbackLocale])) {
            return $translations[$fallbackLocale];
        }

        // Return first available translation
        return array_values($translations)[0] ?? '';
    }

    /**
     * Set localized value in multilingual array
     */
    public static function setLocalizedValue(array $translations = null, string $value = '', string $locale = null): array
    {
        $locale = $locale ?? self::getCurrentLocale();
        $translations = $translations ?? [];
        $translations[$locale] = $value;

        return $translations;
    }
}

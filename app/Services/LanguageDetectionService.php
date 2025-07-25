<?php

namespace App\Services;

use App\Models\Language;

class LanguageDetectionService
{
    /**
     * Detect language of given text
     */
    public function detectLanguage(string $text): string
    {
        // Simple language detection based on character sets
        // Georgian Unicode range: U+10A0–U+10FF
        if (preg_match('/[ა-ჿ]/u', $text)) {
            return 'ka'; // Georgian
        }

        if (preg_match('/[äöüßÄÖÜ]/u', $text)) {
            return 'de'; // German
        }

        // Default to English
        return 'en';
    }

    /**
     * Auto-translate text to all active languages
     */
    public function autoTranslate(string $text, string $sourceLanguage = null): array
    {
        if (!$sourceLanguage) {
            $sourceLanguage = $this->detectLanguage($text);
        }

        $activeLanguages = Language::active()->get();
        $translations = [];

        foreach ($activeLanguages as $language) {
            if ($language->code === $sourceLanguage) {
                // Keep original text for source language
                $translations[$language->code] = $text;
            } else {
                // For now, keep original text instead of showing translation markers
                // This prevents "[Auto-translated]" markers from appearing in the database
                $translations[$language->code] = $text;
            }
        }

        return $translations;
    }
}

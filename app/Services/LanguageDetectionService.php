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
                $translations[$language->code] = $text;
            } else {
                // For now, just copy the original text
                // Later we can integrate Google Translate API or similar
                $translations[$language->code] = $text . ' [Auto-translated to ' . $language->native_name . ']';
            }
        }

        return $translations;
    }
}

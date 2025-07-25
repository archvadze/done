<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\LanguageDetectionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LanguageController extends Controller
{
    /**
     * Detect language and provide translations
     */
    public function detectLanguage(Request $request): JsonResponse
    {
        $service = new LanguageDetectionService();
        
        $texts = $request->input('texts', []);
        $results = [];
        
        foreach ($texts as $text) {
            $detected = $service->detectLanguage($text);
            $translations = $service->autoTranslate($text, $detected);
            
            $results[] = [
                'text' => $text,
                'detected' => $detected,
                'translations' => $translations
            ];
        }
        
        return response()->json($results);
    }
}

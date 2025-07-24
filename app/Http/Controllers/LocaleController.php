<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use App\Models\Language;

class LocaleController extends Controller
{
    /**
     * Switch application locale
     */
    public function switch(Request $request, $locale)
    {
        // Get supported locales from active languages
        $supportedLocales = Language::supportedLocales();
        
        if (!in_array($locale, $supportedLocales)) {
            abort(404, 'Language not supported or inactive');
        }

        // Set locale in session
        Session::put('locale', $locale);
        
        // Redirect back to previous page
        return redirect()->back();
    }

    /**
     * Get current locale data
     */
    public function current()
    {
        $locale = App::getLocale();
        
        // Get active languages from database
        $activeLanguages = Language::active();
        $locales = [];
        
        foreach ($activeLanguages as $language) {
            $locales[$language->code] = [
                'name' => $language->name,
                'flag' => $language->flag_emoji,
                'native' => $language->native_name
            ];
        }

        return response()->json([
            'current' => $locale,
            'data' => $locales[$locale] ?? $locales[Language::default()->code ?? 'en'],
            'available' => $locales
        ]);
    }
}

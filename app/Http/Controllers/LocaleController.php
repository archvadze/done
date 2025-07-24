<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LocaleController extends Controller
{
    /**
     * Switch application locale
     */
    public function switch(Request $request, $locale)
    {
        // Validate locale
        $supportedLocales = config('app.supported_locales', ['en']);
        
        if (!in_array($locale, $supportedLocales)) {
            abort(404);
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
        $locales = [
            'en' => ['name' => 'English', 'flag' => '🇺🇸', 'native' => 'English'],
            'ka' => ['name' => 'Georgian', 'flag' => '🇬🇪', 'native' => 'ქართული'],
            'de' => ['name' => 'German', 'flag' => '🇩🇪', 'native' => 'Deutsch']
        ];

        return response()->json([
            'current' => $locale,
            'data' => $locales[$locale] ?? $locales['en'],
            'available' => $locales
        ]);
    }
}

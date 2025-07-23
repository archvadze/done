<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class LocaleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get supported locales from config
        $supportedLocales = config('app.supported_locales', ['en']);

        // Priority order for locale detection
        $locale = null;

        // 1. Check URL parameter
        if ($request->has('locale') && in_array($request->get('locale'), $supportedLocales)) {
            $locale = $request->get('locale');
            Session::put('locale', $locale);
        }
        // 2. Check session
        elseif (Session::has('locale') && in_array(Session::get('locale'), $supportedLocales)) {
            $locale = Session::get('locale');
        }
        // 3. Check Accept-Language header
        elseif ($request->hasHeader('Accept-Language')) {
            $preferredLanguages = $request->getLanguages();
            foreach ($preferredLanguages as $lang) {
                // Extract language code (e.g., 'en' from 'en-US')
                $langCode = substr($lang, 0, 2);
                if (in_array($langCode, $supportedLocales)) {
                    $locale = $langCode;
                    break;
                }
            }
        }

        // 4. Fallback to default locale
        if (!$locale) {
            $locale = config('app.fallback_locale', 'en');
        }

        // Set the application locale
        App::setLocale($locale);

        return $next($request);
    }
}

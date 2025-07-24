<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class LocalizationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get locale from URL parameter, session, or default
        $locale = $request->get('lang') 
            ?? Session::get('locale') 
            ?? config('app.locale');

        // Validate locale against supported locales
        $supportedLocales = config('app.supported_locales', ['en']);
        if (!in_array($locale, $supportedLocales)) {
            $locale = config('app.locale');
        }

        // Set application locale
        App::setLocale($locale);

        // Store in session for future requests
        if ($request->get('lang')) {
            Session::put('locale', $locale);
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $availableLocales = config('app.supported_locales', []); // بيقرأ من الكونفج

        $browserLocale = substr($request->server('HTTP_ACCEPT_LANGUAGE'), 0, 2);

        if (in_array($browserLocale, $availableLocales)) {
            app()->setLocale($browserLocale);
        } else {
            app()->setLocale('ar');
        }

        return $next($request);
    }
}

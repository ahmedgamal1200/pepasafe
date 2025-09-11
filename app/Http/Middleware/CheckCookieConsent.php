<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCookieConsent
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->cookie('cookie_consent')) {
            // لو مفيش كوكيز موافقة، نمرر معلومة للـ view
            view()->share('showCookieConsent', true);
        }

        return $next($request);
    }
}

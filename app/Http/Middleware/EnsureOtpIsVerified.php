<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureOtpIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {

        if ($request->is('verify/otp') || $request->is('resend-otp')) {
            return $next($request);
        }

        if ($request->cookie('otp')) {
            return redirect()->route('verify.otp')->withErrors([
                'email_otp' => 'يجب تأكيد رمز OTP أولاً.',
            ]);
        }

        return $next($request);
    }
}

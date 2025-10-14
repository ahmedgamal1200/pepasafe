<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromRequest
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {

        $availableLocales = config('app.supported_locales', ['en', 'ar']); // استخدم القيمة الافتراضية إذا لم يتم العثور على الإعداد
        $defaultLocale = config('app.locale', 'ar'); // اللغة الافتراضية

        // 1. التحقق أولاً من وجود لغة مفضلة في الـ Session
        if (Session::has('locale') && in_array(Session::get('locale'), $availableLocales)) {
            app()->setLocale(Session::get('locale'));
        } // 2. إذا لم تكن موجودة في الـ Session، تحقق من لغة المتصفح
        else {
            $browserLocale = substr($request->server('HTTP_ACCEPT_LANGUAGE'), 0, 2);

            if (in_array($browserLocale, $availableLocales)) {
                app()->setLocale($browserLocale);
            } // 3. إذا لم يتم العثور عليها، قم بتعيين اللغة الافتراضية (مثلاً العربية 'ar' كما كانت)
            else {
                app()->setLocale($defaultLocale); // نستخدم القيمة الافتراضية من config
            }
        }

        return $next($request);
    }

}

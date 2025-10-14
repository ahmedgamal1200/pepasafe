<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    public function changeLocale(Request $request, $locale)
    {
        Log::info("Attempting to change locale to: {$locale}");

        // تحقق من أن اللغة مدعومة قبل تعيينها
        if (in_array($locale, Config::get('app.supported_locales', []))) {

            Session::put('locale', $locale);
            Session::get('locale', 'NOT_SET_IN_CONTROLLER');
        }
        return redirect()->back();
    }
}

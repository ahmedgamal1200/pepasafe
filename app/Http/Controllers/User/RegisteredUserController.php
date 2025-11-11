<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Auth\RegisterUserRequest;
use App\Mail\OtpMail;
use App\Models\Setting;
use App\Repositories\RegisteredRepository;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Mail;


class RegisteredUserController extends Controller
{
    public function create(): View|Application|Factory
    {
        return view('users.auth.register-for-user');
    }

    public function store(RegisterUserRequest $request, RegisteredRepository $repo): Application|Redirector|RedirectResponse
    {
        // 1. الحصول على البيانات التي تم التحقق منها (بما في ذلك حقل 'phone' المدمج)
        $validatedData = $request->validated();
        
        // 2. حذف الحقول الإضافية (country_code و phone_number) قبل الإرسال إلى الـ Repository
        unset($validatedData['country_code']);
        unset($validatedData['phone_number']);
        
        // الآن $validatedData تحتوي على حقل 'phone' بالصيغة الكاملة الموحدة
        $user = $repo->register($validatedData);

        event(new Registered($user));

        $emailOtpSetting = Setting::where('key', 'email_otp_active')->first();
        $emailOtpActive = $emailOtpSetting && $emailOtpSetting->value == '1';

        if ($emailOtpActive) {
            // ✅ توليد كود OTP
            $otp = rand(100000, 999999);

            Cookie::queue('otp', $otp, 5);

            Auth::login($user);

            Mail::to($user->email)->send(new OtpMail($otp));

            return redirect()->route('verify.otp');
        } else {
            Auth::login($user);

            return redirect(route('home.users', absolute: false));
        }
    }
}
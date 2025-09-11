<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Eventor\Auth\EventorRegisterRequest;
use App\Mail\OtpMail;
use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\Plan;
use App\Models\Setting;
use App\Repositories\Eventor\Auth\EventorAuthRepository;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $categories = Category::query()->get();
        $plans = Plan::query()->get();
        $paymentMethods = PaymentMethod::query()->get();

        return view('users.auth.register', compact(
            'categories', 'plans', 'paymentMethods'
        ));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Exception
     */
    public function store(EventorRegisterRequest $request, EventorAuthRepository $repo): RedirectResponse
    {

        $user = $repo->registerEventor($request);

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

            return redirect(route('home.eventor', absolute: false));
        }

    }

    public function showOtpForm(): \Illuminate\Contracts\View\View|Application|Factory
    {
        return view('verify-otp-submit');  // صفحة إدخال OTP
    }

    public function verifyOtp(Request $request): RedirectResponse
    {
        // التحقق من المدخلات
        $request->validate([
            'email_otp' => 'required|numeric|digits:6',  // التحقق من OTP للبريد الإلكتروني
            //            'phone_otp' => 'required|numeric|digits:6',  // التحقق من OTP للهاتف
        ]);

        // التحقق من OTP البريد الإلكتروني
        $emailOtp = $request->cookie('otp');

        //        dd($request->input('email_otp'), session('otp_email'));
        if ($request->input('email_otp') !== $emailOtp) {
            return back()->withErrors(['email_otp' => 'الكود المرسل للبريد الإلكتروني غير صحيح.']);
        }

        // التحقق من OTP الهاتف
        //        $phoneOtp = session('otp_phone');
        //        if ($request->input('phone_otp') !== $phoneOtp) {
        //            return back()->withErrors(['phone_otp' => 'الكود المرسل للهاتف غير صحيح.']);
        //        }

        // إذا كانت كل الرموز صحيحة
        //        session()->forget(['otp_email', 'otp_phone']);  // إزالة الـ OTP من الجلسة بعد التحقق
        // ✅ احذف الكود من الكوكي بعد التحقق
        Cookie::queue(Cookie::forget('otp'));

        // متابعة العملية (توجيه المستخدم إلى الصفحة الرئيسية مثلًا)
        if (Auth::user()->hasRole('user')) {
            return redirect()->route('home.users');
        } else {
            return redirect()->route('home.eventor');
        }
    }

    public function resendOtp(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $otp = rand(100000, 999999);

        Cookie::queue('otp', $otp, 5); // صلاحية الكود 5 دقايق

        Mail::to($user->email)->send(new OtpMail($otp));

        return back()->with('success', 'تم إرسال الكود مرة أخرى بنجاح');
    }
}

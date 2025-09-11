<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SocialLoginController extends Controller
{
    public function redirectToGoogle(): RedirectResponse|\Illuminate\Http\RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(): Application|Redirector|\Illuminate\Http\RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Check if the user already exists in your database by google_id
            $user = User::where('google_id', $googleUser->id)->first();

            // If user doesn't exist by google_id, check by email
            if (! $user) {
                $user = User::where('email', $googleUser->email)->first();
            }

            if ($user) {
                // If user exists, update their Google ID and avatar if it's missing
                $user->google_id = $googleUser->id;
                $user->avatar = $googleUser->avatar;
                $user->save();
            } else {
                // If user does not exist, create a new one
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar,
                    'password' => Hash::make(rand(100000, 999999)), // generate a random password for social users
                    // ممكن تضيف أي حقول تانية مطلوبة زي phone أو category_id لو كانت nullable
                ]);
            }

            Auth::login($user); // تسجيل دخول المستخدم

            return redirect()->intended('/home'); // توجيه المستخدم لصفحة بعد تسجيل الدخول
            // استبدل '/dashboard' بالرابط اللي عايزه

        } catch (\Exception $e) {
            Log::error('Google login failed: '.$e->getMessage());

            return redirect('/login')->with('error', 'حدث خطأ أثناء تسجيل الدخول باستخدام جوجل. يرجى المحاولة مرة أخرى.');
        }
    }
}

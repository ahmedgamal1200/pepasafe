<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('users.auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $emailOrPhone = $request->input('email-or-phone');
        $password = $request->input('password');

        $field = filter_var($emailOrPhone, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        if (Auth::attempt([$field => $emailOrPhone, 'password' => $password], $request->filled('remember'))) {
            $request->session()->regenerate();

            $user = auth()->user()->load('roles');

            if ($user->hasAnyRole(['eventor', 'admin', 'super admin', 'employee'])) {
                // لو الرول "eventor"، وجهه لصفحة الـ eventor dashboard مثلاً
                return redirect()->intended(route('home.eventor', absolute: false));
            } else {
                return redirect()->intended(route('home.users', absolute: false));
            }
        }

        return back()->withErrors(['email-or-phone' => 'بيانات الدخول غير صحيحة']);

    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('login');
    }
}

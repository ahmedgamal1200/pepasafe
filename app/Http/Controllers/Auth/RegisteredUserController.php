<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Eventor\Auth\EventorRegisterRequest;
use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\PaymentReceipt;
use App\Models\Plan;
use App\Models\User;
use App\Repositories\Eventor\Auth\EventorAuthRepository;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

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

        return view('users.auth.register', compact
        (
            'categories', 'plans', 'paymentMethods'
        ));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(EventorRegisterRequest $request, EventorAuthRepository $repo): RedirectResponse
    {

        $user = $repo->registerEventor($request);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('home.eventor', absolute: false));
    }
}

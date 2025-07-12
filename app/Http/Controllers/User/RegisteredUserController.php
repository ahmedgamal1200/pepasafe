<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Auth\RegisterUserRequest;
use App\Repositories\RegisteredRepository;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;


class RegisteredUserController extends Controller
{
    public function create(): View|Application|Factory
    {
        return view('users.auth.register-for-user');
    }

    public function store(RegisterUserRequest $request, RegisteredRepository $repo): Application|Redirector|RedirectResponse
    {
        $user = $repo->register($request->validated());

        event(new Registered($user));
        Auth::login($user);

        return redirect(route('home.users', absolute: false));
    }

}

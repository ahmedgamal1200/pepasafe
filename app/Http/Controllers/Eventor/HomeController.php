<?php

namespace App\Http\Controllers\Eventor;

use App\Http\Controllers\Controller;
use App\Models\PhoneNumber;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(): View|Application|Factory
    {
        $user = auth()->user()->load('paymentReceipts', 'subscription');
        $phone = PhoneNumber::query()->get();

        return view('eventors.home', compact('user', 'phone'));
    }
}

<?php

namespace App\Http\Controllers\Eventor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function create()
    {
        $user = Auth::user();
        $plan = $user->subscription->plan;
        $subscription = $user->subscription;

        return view('eventors.events.create-event', compact('user', 'plan', 'subscription'));
    }
}

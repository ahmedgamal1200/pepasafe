<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\PrivacyPolicy;

class PrivacyAndPolicyController extends Controller
{
    public function index()
    {
        $privacyAndPolicy = PrivacyPolicy::query()->get();

        return view('users.privacy-and-policy', compact('privacyAndPolicy'));
    }
}

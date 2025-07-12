<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\PrivacyPolicy;
use App\Models\TermsAndCondition;
use Illuminate\Http\Request;

class AboutController extends Controller
{
    public function index()
    {
        $privacyAndPolicy = PrivacyPolicy::query()->get();

        $faqs = Faq::query()->get();

        $termsAndConditions = TermsAndCondition::query()->get();

        return view('users.about', compact('faqs', 'privacyAndPolicy', 'termsAndConditions'));
    }
}

<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\TermsAndCondition;

class TermsController extends Controller
{
    public function index()
    {
        $termsAndConditions = TermsAndCondition::query()->get();

        return view('users.terms', compact('termsAndConditions'));
    }
}

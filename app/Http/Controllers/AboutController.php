<?php

namespace App\Http\Controllers;

use App\Models\AboutUs;
use App\Models\Faq;

class AboutController extends Controller
{
    public function index()
    {
        $faqs = Faq::query()->get();

        $about = AboutUs::query()->get();

        return view('users.about', compact('faqs', 'about'));
    }
}

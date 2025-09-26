<?php

namespace App\Http\Controllers;

use App\Models\AttendanceDocument;
use App\Models\Document;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function show($uuid): View|Application|Factory
    {
        $document = AttendanceDocument::query()->with(['template.event', 'recipient.user'])
            ->where('uuid', $uuid)->firstOrFail();

        return view('show-attendance', compact('document'));
    }
}

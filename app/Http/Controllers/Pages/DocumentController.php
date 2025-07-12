<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function index(Request $request): View|Application|Factory
    {
        $user = auth()->user();
        $document = null;

        if ($request->has('query')) {
            $document = Document::query()->with(['template.event'])
            ->where('unique_code', $request->query('query'))
                ->orWhere('uuid', $request->query('query'))
            ->first();
        }

        return view('users.home', compact('user', 'document'));
    }

    public function show($uuid): View|Application|Factory
    {
        $document = Document::query()->with(['template.event', 'recipient.user'])
            ->where('uuid', $uuid)->firstOrFail();

        return view('show-document', compact('document'));
    }

//    public function search(Request $request)
//    {
//        $query = $request->input('query');
//
//        $document = Document::query()
//            ->where('unique_code', $query)
//            ->first();
//        if (!$document) {
////            return response()->view('not-found-document');
//            return back()->with('error', 'لم');
//        }
//
////        return view('document-show', compact('document'));
//        return view('home.users', compact('document'));
//    }
}

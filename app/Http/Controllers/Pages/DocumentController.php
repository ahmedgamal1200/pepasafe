<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentTemplate;
use App\Models\Event;
use App\Models\Recipient;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DocumentController extends Controller
{

    public function index(Request $request): View|Factory|Application
    {
        $user = auth()->user();
        $document = null;
        $event = null;
        $templateCount = 0;
        $recipientCount = 0;

        if ($request->has('query')) {
            $query = $request->query('query');

            // البحث عن document بالـ UUID أو الكود
            $document = Document::query()
                ->where('unique_code', $query)
                ->orWhere('uuid', $query)
                ->first();

            // البحث عن event بالـ title
            $event = Event::query()
                ->where('title', 'like', '%' . $query . '%')
                ->first();

            // لو لقينا الحدث، نحسب عدد document_templates المرتبطة بيه
            if ($event) {
                $templateCount = DocumentTemplate::where('event_id', $event->id)->count();
                $recipientCount = Recipient::where('event_id', $event->id)->count();
            }
        }

        return view('users.home', compact('user', 'document', 'event', 'templateCount', 'recipientCount'));

    }



//    public function index(Request $request): View|Application|Factory
//    {
//        $user = auth()->user();
//        $document = null;
//
//        if ($request->has('query')) {
//            $document = Document::query()->with(['template.event'])
//            ->where('unique_code', $request->query('query'))
//                ->orWhere('uuid', $request->query('query'))
//            ->first();
//        }
//
//        return view('users.home', compact('user', 'document'));
//    }

    public function show($uuid): View|Application|Factory
    {
        $document = Document::query()->with(['template.event', 'recipient.user'])
            ->where('uuid', $uuid)->firstOrFail();

        return view('show-document', compact('document'));
    }

    public function toggleVisibility(Document $document): RedirectResponse
    {
//        if ($document->recipient_id !== auth()->id()) {
//            abort(403);
//        }


        $document->visible_on_profile = !$document->visible_on_profile;
        $document->save();

        return back()->with('status', 'document-visibility-toggled');
    }

}

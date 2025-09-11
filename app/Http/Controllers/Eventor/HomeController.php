<?php

namespace App\Http\Controllers\Eventor;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentTemplate;
use App\Models\Event;
use App\Models\PhoneNumber;
use App\Models\Recipient;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(): View|Application|Factory
    {

        $templateCount = 0;
        $recipientCount = 0;
        $templates = collect();

        $user = auth()->user();

        $user->load('paymentReceipts', 'subscription');

        $phone = PhoneNumber::query()->get();

        // عرض الاحداث في صفحة الهوم الخاصة بالاحداث
        if ($user) {
            $events = Event::query()
                ->where('user_id', $user->id)
                ->with(['documentTemplates', 'recipients'])
                ->paginate(12); // 10 عناصر لكل صفحة

            foreach ($events as $event) {
                $templateCount += $event->documentTemplates->count();
                $recipientCount += $event->recipients->count();
            }
        } else {
            $events = collect();
        }

        return view('eventors.home', compact(
            'user',
            'phone',
            'events',
            'recipientCount',
            'templateCount'
        ));
    }

    public function homeForGuests(Request $request): View|Application|Factory
    {
        $user = auth()->user();
        $document = collect();
        $event = collect();
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
                ->where('title', 'like', '%'.$query.'%')
                ->first();

            // لو لقينا الحدث، نحسب عدد document_templates المرتبطة بيه
            if ($event) {
                $templateCount = DocumentTemplate::where('event_id', $event->id)->count();
                $recipientCount = Recipient::where('event_id', $event->id)->count();
            }
        }

        return view('guests.index', compact(
            'user',
            'document',
            'event',
            'templateCount',
            'recipientCount'
        ));

    }
}

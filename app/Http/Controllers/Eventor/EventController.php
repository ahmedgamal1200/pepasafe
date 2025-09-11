<?php

namespace App\Http\Controllers\Eventor;

use App\Http\Controllers\Controller;
use App\Models\AttendanceTemplate;
use App\Models\Document;
use App\Models\DocumentTemplate;
use App\Models\Event;
use App\Models\Recipient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class EventController extends Controller
{
    // في الـ Controller الخاص بك (مثلاً EventController.php)

    public function create()
    {
        $user = Auth::user();
        $subscription = $user->subscription;

        // خطوة أمان: تأكد من وجود اشتراك وباقة لتجنب الأخطاء
        if (!$subscription || !$subscription->plan) {
            // يمكنك توجيه المستخدم لصفحة الاشتراك أو عرض رسالة خطأ
            return redirect()->route('wallet')->with('error', 'يجب أن يكون لديك اشتراك فعال لإنشاء حدث.');
        }

        $plan = $subscription->plan;

        // --- الحسابات الجديدة ---

        // 1. حساب عدد الوثائق المتاحة داخل الباقة
        $priceInPlan = (float) $plan->document_price_in_plan ?? 0;
        $planBalance = (float) $subscription->remaining;
        $docsAvailableInPlan = 0;
        // نتجنب القسمة على صفر إذا كان السعر 0
        if ($priceInPlan > 0) {
            $docsAvailableInPlan = floor($planBalance / $priceInPlan);
        }

        // 2. الرصيد المتاح في المحفظة
        $walletBalance = (float) $subscription->balance;

        // 3. حساب عدد الوثائق التي يمكن شراؤها برصيد المحفظة
        $priceOutsidePlan = (float) $plan->document_price_outside_plan ?? 0;
        $docsAvailableFromWallet = 0;
        // نتجنب القسمة على صفر
        if ($priceOutsidePlan > 0) {
            $docsAvailableFromWallet = floor($walletBalance / $priceOutsidePlan);
        }

        // إرسال كل المتغيرات (القديمة والجديدة) إلى الـ view
        return view('eventors.events.create-event', compact(
            'user',
            'plan',
            'subscription',
            'docsAvailableInPlan',      // <-- المتغير الجديد
            'walletBalance',            // <-- المتغير الجديد
            'docsAvailableFromWallet'   // <-- المتغير الجديد
        ));
    }

    public function show(Event $event, Request $request)
    {

        $user = Auth::user();

        $documents = collect();


        if ($user && $user->subscription) {
            $enable_attendance = $user->subscription->plan->enable_attendance;
        }else{
            $enable_attendance = collect();
        }

        if ($request->has('query')) {
            $query = $request->query('query');

            $user = User::query()
                ->where('name', 'like', "%{$query}%")
                ->orWhere('phone', $query)
                ->orWhere('email', $query)
                ->orWhere('slug', $query)
                ->first();

            if ($user) {
                // هات كل الوثائق اللي ليها recipient مربوط بالـ user ده
                $documents = Document::with('recipient')
                    ->whereHas('recipient', function ($q) use ($user, $event) {
                        $q->where('user_id', $user->id)
                            ->where('event_id', $event->id); // عشان تتأكد انها لنفس الـ event
                    })
                    ->get();
            } else {
                $documents = collect();
            }
        }

        $templateCount = 0;
        $recipientCount = 0;
        $templates = collect();
        $attendances = collect();
        $templateDataFile = collect();


         if ($event) {

                $templates = DocumentTemplate::with(['templateFiles', 'documents'])
                    ->where('event_id', $event->id)->get();
                $attendances = AttendanceTemplate::where('event_id', $event->id)->get();
                $templateCount = $templates->count();
                $recipientCount = Recipient::where('event_id', $event->id)->count();
                $templateDataFile = $event->excelUploads;
            }



         return view('eventors.events.show-event', compact
        (
            'event',
            'templateCount',
            'recipientCount',
             'templates',
             'attendances',
             'user',
            'enable_attendance',
             'templateDataFile',
                'documents'
        ));
    }


    public function toggleAttendance(Request $request)
    {
        try {
            $request->validate([
                'status'   => 'required|boolean',
                'event_id' => 'required|exists:events,id',
            ]);

            // هات كل اليوزرز اللي ليهم recipients مع الـ event_id ده
            $userIds = Recipient::where('event_id', $request->event_id)
                ->pluck('user_id');

            // حدث الحضور لليوزرز دول فقط
            User::whereIn('id', $userIds)->update([
                'is_attendance' => $request->status
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function edit(Event $event)
    {
        $user = Auth::user();

        $subscription = $user->subscription;

        // خطوة أمان: تأكد من وجود اشتراك وباقة لتجنب الأخطاء
        if (!$subscription || !$subscription->plan) {
            // يمكنك توجيه المستخدم لصفحة الاشتراك أو عرض رسالة خطأ
            return redirect()->route('wallet')->with('error', 'يجب أن يكون لديك اشتراك فعال لإنشاء حدث.');
        }

        $plan = $subscription->plan;

        // --- الحسابات الجديدة ---

        // 1. حساب عدد الوثائق المتاحة داخل الباقة
        $priceInPlan = (float) $plan->document_price_in_plan ?? 0;
        $planBalance = (float) $subscription->remaining;
        $docsAvailableInPlan = 0;
        // نتجنب القسمة على صفر إذا كان السعر 0
        if ($priceInPlan > 0) {
            $docsAvailableInPlan = floor($planBalance / $priceInPlan);
        }

        // 2. الرصيد المتاح في المحفظة
        $walletBalance = (float) $subscription->balance;

        // 3. حساب عدد الوثائق التي يمكن شراؤها برصيد المحفظة
        $priceOutsidePlan = (float) $plan->document_price_outside_plan ?? 0;
        $docsAvailableFromWallet = 0;
        // نتجنب القسمة على صفر
        if ($priceOutsidePlan > 0) {
            $docsAvailableFromWallet = floor($walletBalance / $priceOutsidePlan);
        }

        $documentTemplate = DocumentTemplate::with(['documents'])
            ->where('event_id', $event->id)->first();

        return view('eventors.events.edit-event', compact(
            'event'
            , 'user',
            'plan',
            'documentTemplate',
            'subscription',
            'docsAvailableInPlan',      // <-- المتغير الجديد
            'walletBalance',            // <-- المتغير الجديد
            'docsAvailableFromWallet'
        ));
    }

    public function destroy(Request $request, Event $event)
    {
        // ابحث عن المستخدم الذي أنشأ الحدث
        $eventCreator = User::find($event->user_id);

        // تأكد من أن المستخدم الذي أنشأ الحدث موجود
        if (!$eventCreator) {
            return redirect()->back()->with('error', 'حدث خطأ: لم يتم العثور على منشئ الحدث.');
        }

        // تحقق من أن كلمة المرور تم إرسالها
        if (!$request->has('password') || empty($request->password)) {
            return redirect()->back()->with('error', 'يجب إدخال كلمة المرور للتأكيد.');
        }

        // التحقق من كلمة المرور الخاصة بمنشئ الحدث
        if (!Hash::check($request->password, $eventCreator->password)) {
            return redirect()->back()->with('error', 'كلمة المرور غير صحيحة.');
        }

        try {
            $event->delete();

            return redirect()->route('home.eventor')
            ->with('success', 'تم حذف الحدث بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء الحذف');
        }
    }

    // التحكم في اظهار الاحداث في البروفايل او لا من قبل المنظم
    public function toggleVisibility(Event $event)
    {
        $event->visible_on_profile = !$event->visible_on_profile;
        $event->save();

        return back()->with('status', 'event-visibility-toggled');
    }

    public function search(Request $request)
    {
        $user = collect();

        if ($request->has('query')) {
            $query = $request->query('query');

            $user = User::query()
                ->where('name', 'like', "%{$query}%")
                ->orWhere('phone', $query)
                ->orWhere('email', $query)
                ->orWhere('slug', $query)
                ->first();

//            return redirect()->route('showEvent', compact('user'));
        }

        return redirect()->back()->with(compact('user'));
    }




}

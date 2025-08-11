<?php

namespace App\Http\Controllers\Eventor;

use App\Http\Controllers\Controller;
use App\Models\AttendanceTemplate;
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

    public function show(Event $event)
    {
        $templateCount = 0;
        $recipientCount = 0;
        $templates = collect();
        $attendances = collect();

         if ($event) {

                $templates = DocumentTemplate::where('event_id', $event->id)->get();
                $attendances = AttendanceTemplate::where('event_id', $event->id)->get();
                $templateCount = $templates->count();
                $recipientCount = Recipient::where('event_id', $event->id)->count();
            }

         return view('eventors.events.show-event', compact
        (
            'event',
            'templateCount',
            'recipientCount',
             'templates',
             'attendances'
        ));
    }


    public function toggleAttendance(Request $request)
    {
        try {
            $request->validate([
                'template_id' => 'required|exists:document_templates,id',
                'status' => 'required|boolean',
            ]);

            $template = DocumentTemplate::findOrFail($request->template_id);
            $template->is_attendance_enabled = $request->status;
            $template->save();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(), // هنا هيظهر الخطأ الحقيقي
            ], 500);
        }
    }

    public function edit(Event $event)
    {
        $user = Auth::user();
        $plan = $user->subscription->plan;

        $documentTemplate = DocumentTemplate::where('event_id', $event->id)->first();

        return view('eventors.events.edit-event', compact('event', 'user', 'plan', 'documentTemplate'));
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
}

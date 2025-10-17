<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentTemplate;
use App\Models\Event;
use App\Models\Recipient;
use Barryvdh\DomPDF\Facade\Pdf; // 👈 هذا هو الاستيراد الصحيح
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\AttendanceDocument; // استيراد نموذج وثيقة الحضور
use App\Models\AttendanceTemplate;
use Throwable;

// استيراد نموذج قالب الحضور

class DocumentController extends Controller
{

    public function index(Request $request): View|Factory|Application
    {
        $user = auth()->user();
        $document = null;      // ستبقى هذه للوثائق العادية
        $attendanceDocument = null; // جديد: لوثائق الحضور
        $event = null;
        $templateCount = 0;
        $recipientCount = 0;

        if ($request->has('query')) {
            $query = $request->query('query');

            // 1. البحث عن Document (الوثائق العادية) بالـ UUID أو الكود
            $document = Document::query()
                ->where('unique_code', $query)
                ->orWhere('uuid', $query)
                ->first();

            // 2. البحث عن AttendanceDocument (وثائق الحضور) بالـ UUID أو الكود
            $attendanceDocument = AttendanceDocument::query()
                ->where('unique_code', $query) // إذا كان AttendanceDocument يحتوي على unique_code
                ->orWhere('uuid', $query)      // إذا كان AttendanceDocument يحتوي على uuid
                ->first();

            // 3. البحث عن event بالـ title (يبقى كما هو)
            $event = Event::query()
                ->where('title', 'like', '%'.$query.'%')
                ->first();

            // 4. لو لقينا الحدث، نحسب العدد (نجمع الوثائق وقوالب الحضور)
            if ($event) {
                // عدد قوالب الوثائق العادية + عدد قوالب الحضور
                $documentTemplateCount = DocumentTemplate::where('event_id', $event->id)->count();
                $attendanceTemplateCount = AttendanceTemplate::where('event_id', $event->id)->count();
                $templateCount = $documentTemplateCount + $attendanceTemplateCount;

                $recipientCount = Recipient::where('event_id', $event->id)->count();
            }
        }

        // 5. جلب وثائق الوثائق العادية للمستخدم الحالي (للعرض في الصفحة)
        $documents = Document::whereHas('recipient', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        });

        // 6. جلب وثائق الحضور للمستخدم الحالي (للعرض في الصفحة)
        $attendanceDocuments = AttendanceDocument::whereHas('recipient', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        });

        // 7. دمج وتصفيف كل المستندات (Collection Merge)
        // جمع كل المستندات وفرزها حسب تاريخ الإنشاء مثلاً
        $allDocuments = $documents->get()
            ->merge($attendanceDocuments->get())
            ->sortByDesc('created_at');


        $perPage = 10;
        $currentPage = Paginator::resolveCurrentPage() ?: 1;
        $pagedDocuments = $allDocuments->slice(($currentPage - 1) * $perPage, $perPage)->all();

        $documentsForCurrentUser = new LengthAwarePaginator(
            $pagedDocuments,
            $allDocuments->count(),
            $perPage,
            $currentPage,
            ['path' => Paginator::resolveCurrentPath()]
        );

        return view('users.home', compact(
            'user',
            'document',
            'attendanceDocument', // تمرير وثيقة الحضور في حال البحث
            'event',
            'templateCount',
            'recipientCount',
            'documentsForCurrentUser' // يحتوي الآن على الوثائق ووثائق الحضور
        ));
    }

    public function show($uuid): View|Application|Factory
    {
        $document = Document::query()->with(['template.event', 'recipient.user'])
            ->where('uuid', $uuid)->firstOrFail();

        return view('show-document', compact('document'));
    }

    public function toggleVisibility(Document $document): RedirectResponse
    {
        $document->visible_on_profile = ! $document->visible_on_profile;
        $document->save();

        return back()->with('status', 'document-visibility-toggled');
    }

    // في DocumentController.php

    public function calculateDocumentPrice(Request $request)
    {

        $attendanceCharCount = (int) $request->input('attendance_char_count', 0);
        $documentCharCount = (int) $request->input('document_char_count', 0);

        $count = (int) $request->count;

        // --- **البداية: التعديل المطلوب لزيادة الخصم** ---
        // التحقق من تفعيل الحضور من الطلب
        $isAttendanceEnabled = (bool) $request->input('is_attendance_enabled', false);

        // في حال تفعيل الحضور، نضاعف عدد الوثائق المحتسبة للخصم (خصم وثيقتين بدلاً من واحدة)
        if ($isAttendanceEnabled) {
            $count *= 2; // مضاعفة عدد الوثائق
        }
        // --- **النهاية: التعديل المطلوب لزيادة الخصم** ---

        $user = Auth::user();
        $subscription = $user->subscription;
        $plan = $subscription?->plan;

        // في حال عدم وجود اشتراك أو باقة
        if (! $subscription || ! $plan) {
            return response()->json([
                'status' => 'error',
                'message' => 'لا يوجد لديك اشتراك نشط للعثور على تفاصيل التسعير.',
            ]);
        }

        // --- استرجاع البيانات الأساسية ---
        $priceInPlan = (float) $plan->document_price_in_plan ?? 0;
        $priceOutsidePlan = (float) $plan->document_price_outside_plan ?? 0;
        $planBalance = (float) $subscription->remaining; // الرصيد المالي في الباقة
        $walletBalance = (float) $subscription->balance; // رصيد المحفظة
        $smsPriceInPlan = (float) $plan->sms_price_in_plan ?? 0;

        // --- إضافة الجزء الجديد هنا لخصم عدد الأحرف ---
        $totalCharCost = 0;
        $charCostFromPlan = 0;
        $charCostFromWallet = 0;

        if ($attendanceCharCount > 0) {
            $totalCharCost += $attendanceCharCount * $smsPriceInPlan;
        }
        if ($documentCharCount > 0) {
            $totalCharCost += $documentCharCount * $priceInPlan;
        }

        if ($totalCharCost > 0) {
            // خصم تكلفة الأحرف من رصيد الباقة أولاً
            if ($planBalance >= $totalCharCost) {
                $planBalance -= $totalCharCost;
                $charCostFromPlan = $totalCharCost;
            } else {
                $remainingCharCost = $totalCharCost - $planBalance;
                $charCostFromPlan = $planBalance;
                $planBalance = 0;
                // خصم الباقي من رصيد المحفظة
                $walletBalance -= $remainingCharCost;
                $charCostFromWallet = $remainingCharCost;
            }
        }
        // --- نهاية الجزء الجديد ---

        // --- حساب التكلفة الإجمالية داخل الباقة ---
        $totalCost = $count * $priceInPlan;

        // --- السيناريو 1: رصيد الباقة كافٍ لتغطية كل الوثائق ---
        if ($planBalance >= $totalCost) {
            $remainingPlanBalance = $planBalance - $totalCost;

            return response()->json([
                'status' => 'in_plan',
                'docs_count' => $count,
                'total_cost' => $totalCost,
                'plan_balance_after' => $remainingPlanBalance,
                'char_cost_from_plan' => $charCostFromPlan, // تمت الإضافة
                'char_cost_from_wallet' => $charCostFromWallet,
            ]);
        }

        $docsCoveredByPlan = 0;
        if ($priceInPlan > 0) {
            // عدد الوثائق التي يمكن لرصيد الباقة تغطيتها
            $docsCoveredByPlan = floor($planBalance / $priceInPlan);
        }

        $extraDocs = $count - $docsCoveredByPlan; // عدد الوثائق التي ستُحسب من المحفظة
        $extraCost = $extraDocs * $priceOutsidePlan; // تكلفتها

        // التحقق مما إذا كان رصيد المحفظة كافياً للتكلفة الإضافية
        if ($walletBalance >= $extraCost) {
            $remainingWalletBalance = $walletBalance - $extraCost;

            return response()->json([
                'status' => 'partial_plan',
                'docs_count' => $count,
                'covered_by_plan_count' => $docsCoveredByPlan, // عدد الوثائق المغطاة
                'extra_docs_count' => $extraDocs,
                'extra_cost' => $extraCost,
                'current_wallet_balance' => $walletBalance,
                'wallet_balance_after' => $remainingWalletBalance,
                'char_cost_from_plan' => $charCostFromPlan, // تمت الإضافة
                'char_cost_from_wallet' => $charCostFromWallet,
            ]);
        }

        // --- السيناريو 3: الأرصدة غير كافية ---
        return response()->json([
            'status' => 'insufficient_funds',
            'message' => "رصيدك غير كافٍ. باقتك تغطي {$docsCoveredByPlan} وثيقة فقط. أنت بحاجة إلى {$extraCost} جنيه في محفظتك لتغطية الباقي، ورصيدك الحالي هو {$walletBalance} جنيه فقط.",
            'char_cost_from_plan' => $charCostFromPlan, // تمت الإضافة
            'char_cost_from_wallet' => $charCostFromWallet, // تمت الإضافة
            ]);
    }

    /**
     * @throws Throwable
     */
    public function downloadAll(DocumentTemplate $template)
    {
        // 1. تصفية المستندات بناءً على event_id للقالب المُمرر
        $eventId = $template->event_id;

        // جلب جميع المستندات التي تنتمي لأي قالب مرتبط بنفس الـ event_id
        $documents = Document::whereHas('template', function ($query) use ($eventId) {
            $query->where('event_id', $eventId);
        })->get();

        if ($documents->isEmpty()) {
            return back()->with('error', 'لا توجد شهادات لهذا الحدث لتنزيلها.');
        }

        // 2. تجميع محتوى HTML لجميع الشهادات (صور مشفرة بـ Base64)
        $combinedHtml = '';

        foreach ($documents as $document) {

            // ⚠️ مهم: تأكد أن 'file_path' هو الحقل الصحيح الذي يحمل اسم ملف الشهادة
            $certificateFileName = $document->file_path ?? 'placeholder.jpg';
            $fullCertificatePath = public_path('storage/' . $certificateFileName);
            $base64Image = '';

            if (file_exists($fullCertificatePath)) {
                $base64Image = base64_encode(file_get_contents($fullCertificatePath));
            }

            // تمرير بيانات التشفير إلى الـ View
            $documentHtml = view('templates.certificate', [
                'base64Image' => $base64Image,
                'certificateFileName' => $certificateFileName,
            ])->render();

            $combinedHtml .= $documentHtml;

            // إضافة فاصل صفحة بعد كل شهادة باستثناء الأخيرة
            if (!$document->is($documents->last())) {
                // فاصل صفحة بسيط لضمان بداية كل شهادة في صفحة جديدة
                $combinedHtml .= '<div style="page-break-after: always; height: 1px;"></div>';
            }
        }

        // 3. توليد ملف PDF واحد وإرساله للتنزيل
        try {
            ini_set('memory_limit', '512M'); // زيادة الذاكرة للمستندات الكبيرة

            // تعيين خيارات Dompdf الضرورية لمعالجة Base64 والملفات المحلية
            $pdf = Pdf::setOptions([
                'isPhpEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ])->loadHTML($combinedHtml);

            // يمكنك ضبط حجم الورقة هنا (مثل A4)
//            $pdf->setPaper('A4', 'portrait');

            $fileName = 'All_Documents_' . now()->format('Y-m-d_H-i-s') . '.pdf';
            $storagePath = 'public/' . $fileName;

            // الحفظ باستخدام Facade Storage
            Storage::put($storagePath, $pdf->output());

            // الحصول على المسار الكامل للتنزيل
            $downloadPath = Storage::path($storagePath);

            // إرسال ملف PDF للتنزيل وحذفه بعد الإرسال
            return response()->download($downloadPath, $fileName)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            Log::error('PDF generation failed: ' . $e->getMessage());
            return back()->with('error', 'فشل في توليد ملف PDF الموحد: ' . $e->getMessage());
        }
    }

    /**
     * @throws Throwable
     */
    protected function getDocumentHtmlContent($document): string
    {
        return view('templates.certificate', ['document' => $document])->render();
    }
}

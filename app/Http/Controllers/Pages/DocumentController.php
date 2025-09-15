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
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use ZipArchive;
use App\Models\AttendanceDocument; // استيراد نموذج وثيقة الحضور
use App\Models\AttendanceTemplate; // استيراد نموذج قالب الحضور

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
        $count = (int) $request->count;
        if ($count <= 0) {
            return response()->json(['status' => 'error', 'message' => 'الرجاء تقديم عدد وثائق صالح.']);
        }

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

        \Log::info('Wallet balance is: '.$walletBalance);

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
            ]);
        }

        // --- السيناريو 3: الأرصدة غير كافية ---
        return response()->json([
            'status' => 'insufficient_funds',
            'message' => "رصيدك غير كافٍ. باقتك تغطي {$docsCoveredByPlan} وثيقة فقط. أنت بحاجة إلى {$extraCost} جنيه في محفظتك لتغطية الباقي، ورصيدك الحالي هو {$walletBalance} جنيه فقط.",
        ]);
    }

    public function downloadAll(DocumentTemplate $template)
    {
        // 1. إنشاء ملف Zip
        $zipFile = storage_path('app/public/documents.zip');
        $zip = new ZipArchive;

        // إذا كان الملف غير موجود، قم بإنشائه
        if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {

            // 2. تحديد اسم المجلد الذي تريد إنشاءه داخل ملف Zip
            $folderName = 'Documents-'.now()->format('Y-m-d'); // مثال: "شهادات-2023-10-27"

            // 3. إضافة الوثائق إلى المجلد داخل ملف Zip
            foreach ($template->documents as $document) {
                // الحصول على المسار الكامل للملف في نظام الملفات
                $filePath = storage_path('app/public/'.$document->file_path);

                // التأكد من وجود الملف
                if (file_exists($filePath)) {
                    // إضافة الملف إلى المجلد داخل الـ Zip باستخدام المسار الثاني
                    $zip->addFile($filePath, $folderName.'/'.basename($filePath));
                }
            }
            $zip->close();

            // 4. إرسال ملف Zip للتنزيل
            return response()->download($zipFile)->deleteFileAfterSend(true);
        }

        return back()->with('error', 'فشل في إنشاء ملف التنزيل.');
    }
}

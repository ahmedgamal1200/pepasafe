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
use Illuminate\Support\Facades\Auth;

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

    public function show($uuid): View|Application|Factory
    {
        $document = Document::query()->with(['template.event', 'recipient.user'])
            ->where('uuid', $uuid)->firstOrFail();

        return view('show-document', compact('document'));
    }

    public function toggleVisibility(Document $document): RedirectResponse
    {
        $document->visible_on_profile = !$document->visible_on_profile;
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
        if (!$subscription || !$plan) {
            return response()->json([
                'status' => 'error',
                'message' => 'لا يوجد لديك اشتراك نشط للعثور على تفاصيل التسعير.'
            ]);
        }

        // --- استرجاع البيانات الأساسية ---
        $priceInPlan = (float) $plan->document_price_in_plan ?? 0;
        $priceOutsidePlan = (float) $plan->document_price_outside_plan ?? 0;
        $planBalance = (float) $subscription->remaining; // الرصيد المالي في الباقة
        $walletBalance = (float) $subscription->balance; // رصيد المحفظة

        \Log::info('Wallet balance is: ' . $walletBalance);


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
                'wallet_balance_after' => $remainingWalletBalance
            ]);
        }

        // --- السيناريو 3: الأرصدة غير كافية ---
        return response()->json([
            'status' => 'insufficient_funds',
            'message' => "رصيدك غير كافٍ. باقتك تغطي {$docsCoveredByPlan} وثيقة فقط. أنت بحاجة إلى {$extraCost} جنيه في محفظتك لتغطية الباقي، ورصيدك الحالي هو {$walletBalance} جنيه فقط."
        ]);
    }

}

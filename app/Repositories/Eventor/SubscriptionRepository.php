<?php

namespace App\Repositories\Eventor;

use Illuminate\Support\Facades\Auth;

class SubscriptionRepository
{

    public function hasEnoughBalance(int $count, bool $isAttendanceEnabled = false): bool
    {
        if ($isAttendanceEnabled) {
            $count *= 2;
        }

        $user = Auth::user();
        $subscription = $user->subscription;
        $plan = $subscription?->plan;

        if (!$subscription || !$plan) {
            return false;
        }

        $priceInPlan = (float) $plan->document_price_in_plan ?? 0;
        $priceOutsidePlan = (float) $plan->document_price_outside_plan ?? 0;
        $planBalance = (float) $subscription->remaining;
        $walletBalance = (float) $subscription->balance;

        $totalCostInPlan = $count * $priceInPlan;

        // السيناريو 1: الباقة تغطي كل شيء
        if ($planBalance >= $totalCostInPlan) {
            return true;
        }

        // السيناريو 2: تغطية جزئية
        $docsCoveredByPlan = 0;
        if ($priceInPlan > 0) {
            $docsCoveredByPlan = floor($planBalance / $priceInPlan);
        }

        $extraDocs = $count - $docsCoveredByPlan;
        $extraCost = $extraDocs * $priceOutsidePlan;

        // التحقق مما إذا كانت المحفظة تغطي التكلفة الإضافية
        if ($walletBalance >= $extraCost) {
            return true;
        }

        // السيناريو 3: رصيد غير كافٍ
        return false;
    }

    /**
     * Charge the user for document generation, deducting from plan first, then wallet.
     * Applies attendance multiplier if enabled.
     */
    public function chargeDocument(int $count, bool $isAttendanceEnabled = false): bool
    {
        $user = Auth::user();
        $subscription = $user->subscription;
        $plan = $subscription?->plan;

        // لا يمكن الخصم في حال عدم وجود اشتراك أو باقة
        if (!$subscription || !$plan) {
            return false;
        }

        // **[تعديل: مضاعفة عدد الوثائق إذا كان الحضور مفعلاً لخصم ضعف التكلفة]**
        if ($isAttendanceEnabled) {
            $count *= 2;
        }

        $priceInPlan = (float) $plan->document_price_in_plan ?? 0;
        $priceOutsidePlan = (float) $plan->document_price_outside_plan ?? 0;

        $planBalance = (float) $subscription->remaining;
        $walletBalance = (float) $subscription->balance;

        // --- حساب التكلفة الإجمالية داخل الباقة ---
        $totalCostInPlan = $count * $priceInPlan;

        // --- السيناريو 1: رصيد الباقة كافٍ لتغطية كل الوثائق ---
        if ($planBalance >= $totalCostInPlan) {
            // الخصم الفعلي من رصيد الباقة
            $subscription->decrement('remaining', $totalCostInPlan);
            return true;
        }

        // --- السيناريو 2: تغطية جزئية ---
        $docsCoveredByPlan = 0;
        if ($priceInPlan > 0) {
            // عدد الوثائق التي يمكن لرصيد الباقة تغطيتها
            $docsCoveredByPlan = floor($planBalance / $priceInPlan);
        }

        // --- الخصم من الباقة أولاً (يتم خصم كل ما تبقى) ---
        if ($planBalance > 0) {
            $subscription->decrement('remaining', $planBalance);
        }

        $extraDocs = $count - $docsCoveredByPlan; // عدد الوثائق التي ستُحسب من المحفظة

        // حساب تكلفة الوثائق الإضافية التي ستخصم من المحفظة
        $extraCost = $extraDocs * $priceOutsidePlan;

        // --- التحقق مما إذا كان رصيد المحفظة كافياً للتكلفة الإضافية والخصم منها ---
        if ($walletBalance >= $extraCost) {
            // الخصم الفعلي من رصيد المحفظة (المخزن في $subscription->balance حسب الكود الأصلي)
            $subscription->decrement('balance', $extraCost);
            return true;
        }

        // --- السيناريو 3: الأرصدة غير كافية ---
        return false;
    }
}

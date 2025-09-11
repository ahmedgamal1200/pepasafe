<?php

namespace App\Repositories\Eventor;

use Illuminate\Support\Facades\Auth;

class SubscriptionRepository
{
    //    public function checkBalance(int $required): bool
    //    {
    //        $user = Auth::user();
    //        $subscription = $user->subscription;
    //        return $subscription->remaining >= $required;
    //    }

    public function hasEnoughBalance(int $count): bool
    {
        $user = Auth::user();
        $subscription = $user->subscription;

        $priceInPlan = $subscription?->plan->document_price_in_plan;
        $priceOutsidePlan = $subscription?->plan->document_price_outside_plan;

        $totalInPlan = $priceInPlan * $count;
        $totalOutsidePlan = $priceOutsidePlan * $count;

        return
            ($subscription && $subscription->remaining >= $totalInPlan) ||
            ($subscription && $subscription->balance >= $totalOutsidePlan);
    }

    /**
     * Charge the user for a document generation.
     */
    public function chargeDocument(int $count): bool
    {
        $user = Auth::user();
        $subscription = $user->subscription;

        $priceInPlan = $subscription?->plan->document_price_in_plan ?? 0;
        $priceOutsidePlan = $subscription?->plan->document_price_outside_plan ?? 0;

        $totalInPlan = $priceInPlan * $count;
        $totalOutsidePlan = $priceOutsidePlan * $count;

        // التأكد من وجود اشتراك و من ان تمن الوثيقة الخاصة ب الباقة يساوي او اكبر من الرصيد
        // خصم من الباقة لو عنده رصيد كافي فيها
        if ($subscription && $subscription->remaining >= $totalInPlan) {
            $subscription->decrement('remaining', $totalInPlan);

            return true;
        }

        // لو مفيش اشتراك أو الرصيد مش كافي في الباقة

        if ($subscription->balance >= $totalOutsidePlan) {
            $user->decrement('balance', $totalOutsidePlan);

            return true;
        }

        return false;
    }
}

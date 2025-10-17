<?php

namespace App\Services;

use App\Repositories\Eventor\SubscriptionRepository;

class SubscriptionService
{
    protected SubscriptionRepository $subscriptionRepository;

    public function __construct(SubscriptionRepository $subscriptionRepository)
    {
        $this->subscriptionRepository = $subscriptionRepository;
    }

    /**
     * التحقق من توفر رصيد كافٍ للوثائق المطلوبة، مع الأخذ في الاعتبار تفعيل الحضور.
     */
    public function hasEnoughBalance(int $required, bool $isAttendanceEnabled = false): bool
    {
        // تم تمرير المُعامل الإضافي إلى الـ Repository
        return $this->subscriptionRepository->hasEnoughBalance($required, $isAttendanceEnabled);
    }

    /**
     * خصم تكلفة الوثائق بشكل فعلي، مع الأخذ في الاعتبار تفعيل الحضور.
     */
    public function chargeDocument(int $amount, bool $isAttendanceEnabled = false): void
    {
        // تم تمرير المُعامل الإضافي إلى الـ Repository
        $this->subscriptionRepository->chargeDocument($amount, $isAttendanceEnabled);
    }
}

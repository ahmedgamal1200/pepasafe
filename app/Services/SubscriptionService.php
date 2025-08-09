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

    public function hasEnoughBalance(int $required): bool
    {
        return $this->subscriptionRepository->hasEnoughBalance($required);
    }

    public function chargeDocument(int $amount): void
    {
        $this->subscriptionRepository->chargeDocument($amount);
    }
}

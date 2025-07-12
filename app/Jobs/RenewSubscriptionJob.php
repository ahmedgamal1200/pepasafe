<?php

namespace App\Jobs;

use App\Events\SubscriptionRenewed;
use App\Models\Subscription;
use App\Models\SubscriptionHistory;
use App\Notifications\InsufficientBalanceNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class RenewSubscriptionJob implements ShouldQueue
{
    use Queueable;

    protected $subscriptionId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $subscriptionId)
    {
        $this->subscriptionId = $subscriptionId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $old = Subscription::with(['user', 'plan'])->find($this->subscriptionId);

        if (!$old) {
            Log::error("RenewSubscriptionJob: Subscription with ID {$this->subscriptionId} not found.");
            return;
        }

        $plan = $old->plan;

        if((float)$old->balance < (float)$plan->price){
            // لو مفيش رصيد كافيء نحدث الحالة ونبعت اشعار
            $old->update(['status' => 'pending']);
            $old->user->notify(new InsufficientBalanceNotification($old->plan));
            return;
        }

        $old->update(['status' => 'expired']);

        subscriptionHistory::query()->create([
            'subscription_id' => $old->id, // هنا بنربط السجل بالـ ID بتاع الاشتراك القديم
            'status' => 'expired', // حالته دلوقتي 'expired'
            'type' => 'expired', // نوع العملية ممكن يكون 'expired' أو 'termination'
            'start_date' => $old->start_date, // تاريخ بداية الاشتراك القديم
            'end_date' => $old->end_date,   // تاريخ نهاية الاشتراك القديم
        ]);


        $new = Subscription::query()->create([
            'plan_id' => $plan->id,
            'user_id' => $old->user_id,
            'balance' => $plan->credit_amount,
            'remaining' => $plan->credit_amount,
            'start_date' => now(),
            'end_date' => now()->addDays($plan->duration_days),
            'status' => 'active',
            'auto_renew' => true,
        ]);

        subscriptionHistory::query()->create([
            'subscription_id' => $new->id,
            'status' => 'active',
            'type' => 'renewal',
            'start_date' => $new->start_date,
            'end_date' => $new->end_date,
            'renewal_date' => now(),
        ]);

        event(new SubscriptionRenewed($old, $new));
    }
}

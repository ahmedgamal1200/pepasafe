<?php

namespace App\Services;

use App\Mail\SubscriptionRenewedMail;
use App\Models\User;
use App\Notifications\InsufficientBalanceNotification;
use App\Notifications\SubscriptionRenewedNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class RenewSubscriptionNow
{
    public function renewNow(User $user): true|string
    {
        $subscription = $user->subscription;

        $plan = $subscription->plan;

        if (! $subscription || ! $plan) {
            return 'الباقة غير موجودة ';
        }

        if ($subscription->balance < $plan->price) {
            $subscription->update(['status' => 'pending']);
            $user->notify(new InsufficientBalanceNotification($plan));

            return ' الرصيد غير كافٍ لتجديد الباقة. يُرجى شحن المحفظة ثم إعادة التجديد.';
        }

        try {
            DB::transaction(function () use ($user, $subscription, $plan) {
                $subscription->balance -= $plan->price;

                $subscription->update([
                    'status' => 'active',
                    'start_date' => now(),
                    'end_date' => now()->addDays($plan->duration_days ?? 30),
                    'remaining' => $plan->carry_over_credit
                        ? $subscription->remaining + $plan->credit_amount
                        : $plan->credit_amount,
                ]);

                $user->save();

                // ارسال اشعار ان تم تجديد الباقة بنجاح
                $user->notify(new SubscriptionRenewedNotification($subscription, $plan));

                // ارسال ايميل ان تم تجديد الباقة بنجاح
                Mail::to($user->email)->send(new SubscriptionRenewedMail($subscription, $plan));
            });

            return true;
        } catch (\Exception $e) {
            report($e);

            return 'حدث خطأ أثناء تجديد الباقة. يرجى المحاولة مرة أخرى. أو تواصل مع الدعم الفني.';
        }

    }
}

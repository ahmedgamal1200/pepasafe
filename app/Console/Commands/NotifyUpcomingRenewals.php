<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Notifications\SubscriptionWillRenewNotification;
use Illuminate\Console\Command;

class NotifyUpcomingRenewals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:notify-upcoming-renewals';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify users that their subscription will renew tomorrow';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tomorrow = now()->addDay()->toDateTimeString();

        $subscriptions = Subscription::query()->where('auto_renew', true)
            ->where('status', 'active')
            ->whereDate('end_date', $tomorrow)
            ->get();

        foreach ($subscriptions as $subscription) {
            $user = $subscription->user;
            $plan = $subscription->plan;

            // Notification via database
            $user->notify(new SubscriptionWillRenewNotification($subscription));
        }
    }
}

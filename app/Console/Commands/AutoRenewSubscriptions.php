<?php

namespace App\Console\Commands;

use App\Jobs\RenewSubscriptionJob;
use App\Models\Subscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AutoRenewSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:auto-renew';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Queue subscription renewals with auto_renew enabled';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $subscriptions = Subscription::with(['user', 'plan'])
            ->whereDate('end_date', today())
            ->where('auto_renew', true)
            ->where('status', 'active')
            ->get();
        Log::info('AutoRenewSubscriptions Command: Found '.$subscriptions->count().' subscriptions to process.');

        foreach ($subscriptions as $subscription) {
            RenewSubscriptionJob::dispatch($subscription->id);
        }
    }
}

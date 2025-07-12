<?php

use App\Console\Commands\AutoRenewSubscriptions;
use App\Jobs\ScheduleDashboardNotificationJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command(AutoRenewSubscriptions::class)->daily();

//Schedule::job(ScheduleDashboardNotificationJob::class)
//    ->everyMinute()
//    ->timezone('Africa/Cairo');


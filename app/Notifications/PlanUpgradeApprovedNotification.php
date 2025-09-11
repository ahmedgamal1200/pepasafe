<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PlanUpgradeApprovedNotification extends Notification
{
    use Queueable;

    protected $planUpgradeRequest;

    public function __construct($planUpgradeRequest)
    {
        $this->planUpgradeRequest = $planUpgradeRequest;
    }

    // القنوات اللي هيتبعت عليها الاشعار
    public function via($notifiable): array
    {
        return ['database']; // ممكن تضيف mail أو broadcast كمان
    }

    // البيانات اللي هتتخزن في جدول notifications
    public function toDatabase($notifiable): array
    {
        return [
            'title' => 'تمت الموافقة على ترقية الباقة',
            'message' => '<span style="color:green;">تمت الموافقة على طلب ترقية باقتك إلى: '
                .($this->planUpgradeRequest->plan->name ?? '')
                .'</span>',
            'plan_id' => $this->planUpgradeRequest->plan->id ?? null,
        ];
    }
}

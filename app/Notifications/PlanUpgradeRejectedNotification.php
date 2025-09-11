<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PlanUpgradeRejectedNotification extends Notification
{
    use Queueable;

    protected $planUpgradeRequest;

    public function __construct($planUpgradeRequest)
    {
        $this->planUpgradeRequest = $planUpgradeRequest;
    }

    public function via($notifiable): array
    {
        return ['database']; // ممكن تضيف mail لو عاوز يوصل بريد كمان
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title'   => 'تم رفض طلب ترقية الباقة',
            'message' => 'نأسف، تم رفض طلب ترقية باقتك. السبب: <span style="color:red;">'
                . ($this->planUpgradeRequest->rejected_reason ?? '—')
                . '</span>',
            'plan_id' => $this->planUpgradeRequest->plan->id ?? null,
        ];
    }
}

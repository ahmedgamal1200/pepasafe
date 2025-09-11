<?php

namespace App\Services;

use App\Http\Requests\StorePlanUpgradeRequest;
use App\Mail\ApprovePlanUpgradeRequestMail;
use App\Mail\PlanUpgradeRequestMail;
use App\Models\PlanUpgradeRequest;
use App\Notifications\PlanUpgradeApprovedNotification;
use App\Notifications\PlanUpgradeRejectedNotification;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Mail;

class PlanUpgradeRequestService
{
    public function store(StorePlanUpgradeRequest $request)
    {
        try {
            // الوصول إلى كل الملفات المرفوعة
            $uploadedFiles = $request->file();

            // العثور على الملف اللي اسمه بيبدأ بـ "receipt_path"
            $receiptFile = null;
            foreach ($uploadedFiles as $name => $file) {
                if (str_starts_with($name, 'receipt_path')) {
                    $receiptFile = $file;
                    break;
                }
            }

            // لو فيه ملف تم رفعه، يتم تخزينه
            if ($receiptFile) {
                $receiptPath = $receiptFile->store('PlanUpgradeRequestReceipt', 'public');
            } else {
                // التعامل مع الحالة اللي مفيش فيها ملف
                // ممكن ترمي exception أو تخزن null في قاعدة البيانات
                $receiptPath = null;
            }

            $data = $request->validated();
            $data['receipt_path'] = $receiptPath;
            $data['user_id'] = $request->user()->id;
            $data['status'] = 'pending';
            $data['plan_id'] = $request->input('plan_id');
            $data['subscription_id'] = $request->input('subscription_id');

            // تأكد من حذف السطر ده
            // unset($data['receipt_path']);

            return PlanUpgradeRequest::query()->create($data);

        } catch (Exception $e) {
            throw new \RuntimeException(
                'فشل إنشاء طلب ترقية الباقة: '.$e->getMessage(),
                0,
                $e
            );
        }
    }

    public function approve(PlanUpgradeRequest $upgrade): void
    {
        // الحصول على الاشتراك الحالي
        $subscription = $upgrade->subscription;

        // الحصول على الباقة الجديدة بالكامل (الـ Model)
        $newPlan = $upgrade->plan;

        // تأكد إن الباقة موجودة قبل ما تكمل
        if (! $newPlan) {
            // ممكن ترمي استثناء أو تتعامل مع الحالة دي بطريقة مناسبة
            throw new \Exception('New plan not found.');
        }

        $currentEndDate = Carbon::parse($subscription->end_date);

        // إذا كان تاريخ انتهاء الاشتراك الحالي في الماضي، ابدأ من الآن
        if ($currentEndDate->isPast()) {
            $currentEndDate = now();
        }

        // نضيف عدد الأيام من الباقة الجديدة
        $newEndDate = $currentEndDate->addDays($newPlan->duration_days);

        // تحديث الاشتراك: تغيير الـ plan_id وزيادة الرصيد
        $subscription->update([
            'plan_id' => $newPlan->id,
            'remaining' => $subscription->remaining + $newPlan->credit_amount,
            'end_date' => $newEndDate, // تمديد الاشتراك بشهر
            'start_date' => now(), // تحديث تاريخ البداية إلى الآن
            'status' => 'active', // تأكد من أن الحالة نشطة
            'auto_renew' => false, // تفعيل التجديد التلقائي
        ]);

        $upgrade->update([
            'status' => 'approved',
        ]);

        Mail::to($upgrade->user->email)->send(new ApprovePlanUpgradeRequestMail($upgrade));
        $upgrade->user->notify(new PlanUpgradeApprovedNotification($upgrade));
    }

    public function reject(
        PlanUpgradeRequest $upgrade,
        string $rejectedReason,
        bool $sendEmail
    ): void {
        $upgrade->update([
            'status' => 'rejected',
            'rejected_reason' => $rejectedReason,
        ]);
        if ($sendEmail) {
            Mail::to($upgrade->user->email)->send(
                new PlanUpgradeRequestMail($upgrade)
            );
        }

        $upgrade->user->notify(new PlanUpgradeRejectedNotification($upgrade));

    }
}

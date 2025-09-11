<?php

namespace App\Observers;

use App\Models\Plan;

class PlanObserver
{
    public function created(Plan $plan): void
    {
        // التحقق مما إذا كان حقل 'user_id' موجودًا وله قيمة
        if (isset($plan->user_id) && $plan->user_id !== null) {
            // تحديث قيمة is_public إلى false وحفظ التغيير
            $plan->is_public = false;
            $plan->save(); // مهم جدًا: يجب حفظ التغيير يدويًا
        }
    }

    /**
     * Handle the Plan "updated" event.
     */
    public function updated(Plan $plan): void
    {
        //
    }

    /**
     * Handle the Plan "deleted" event.
     */
    public function deleted(Plan $plan): void
    {
        //
    }

    /**
     * Handle the Plan "restored" event.
     */
    public function restored(Plan $plan): void
    {
        //
    }

    /**
     * Handle the Plan "force deleted" event.
     */
    public function forceDeleted(Plan $plan): void
    {
        //
    }
}

<?php

namespace App\Http\Controllers\Eventor\Wallet;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePlanUpgradeRequest;
use App\Models\PlanUpgradeRequest;
use App\Services\PlanUpgradeRequestService;
use Illuminate\Http\RedirectResponse;
use Throwable;

class PlanUpgradeRequestController extends Controller
{
    public function __construct(protected PlanUpgradeRequestService $service)
    {
        //
    }

    public function upgrade(StorePlanUpgradeRequest $request)
    {
        //        dd($request->all());
        try {
            $this->service->store($request);

            return redirect()->back()->with('success', 'تم إرسال طلب ترقية الباقة بنجاح.');

        } catch (Throwable $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function approve(PlanUpgradeRequest $upgrade): RedirectResponse
    {
        $this->service->approve($upgrade);

        return back()->with('success', 'تم الموافقة علي الشحن ');
    }

    public function reject(PlanUpgradeRequest $upgrade): RedirectResponse
    {
        $this->service->reject($upgrade);

        return back()->with('info', 'تم رفض طلب الشحن راجع البريد الإلكتروني الخاص بك');
    }
}

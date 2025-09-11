<?php

namespace App\Http\Controllers\Eventor\Wallet;

use App\Http\Controllers\Controller;
use App\Http\Requests\Eventor\Wallet\StoreWalletRechargetRequest;
use App\Models\WalletRechargeRequest;
use App\Services\WalletRechargeRequestService;
use Illuminate\Http\RedirectResponse;

class WalletRechargeRequestController extends Controller
{
    public function __construct(protected WalletRechargeRequestService $service)
    {
        //
    }
    public function store(StoreWalletRechargetRequest $request): RedirectResponse
    {
        $this->service->create($request->validated(), $request);

        return back()->with('success', 'تم إرسال طلب الشحن بنجاح . جاري مراجعة طلبك...');
    }

    public function approve(WalletRechargeRequest $walletRechargeRequest): RedirectResponse
    {
        $this->service->approve($walletRechargeRequest);

        return back()->with('success', 'تم الموافقة علي الشحن ');
    }

    public function reject(WalletRechargeRequest $walletRechargeRequest): RedirectResponse
    {
        $this->service->reject($walletRechargeRequest);

        return back()->with('info', 'تم رفض طلب الشحن راجع البريد الإلكتروني الخاص بك');
    }
}

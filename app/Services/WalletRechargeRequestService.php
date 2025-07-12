<?php

namespace App\Services;

use App\Http\Requests\Eventor\Wallet\StoreWalletRechargetRequest;
use App\Mail\WalletRechargeRequestRejected;
use App\Models\Subscription;
use App\Models\WalletRechargeRequest;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Mail;

class WalletRechargeRequestService
{
    public function create(array $data, StoreWalletRechargetRequest $request)
    {
        $data = $request->validated();

        $data['receipt_path'] = $request->file('receipt_path')
            ->store('walletRechargeRequestReceipt', 'public');
        $data['user_id'] = $request->user()->id;
        $data['status'] = 'pending';
        $data['amount'] = $request->input('amount');
        $data['plan_id'] = $request->input('plan_id');
        $data['subscription_id'] = $request->input('subscription_id');

        unset($data['receipt']);
        return WalletRechargeRequest::query()->create($data);
    }

    public function approve(WalletRechargeRequest $walletRechargeRequest, int $reviewedById): void
    {
        $walletRechargeRequest->subscription
            ->increment('balance', $walletRechargeRequest->amount);

        $walletRechargeRequest->update([
            'status' => 'approved',
            'approved_at' => now(),
            'reviewed_by' => $reviewedById,
        ]);
    }

    public function reject(
        WalletRechargeRequest $walletRechargeRequest,
        string $adminNote,
        bool $sendEmail
    ): void
    {
        $walletRechargeRequest->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'admin_note' => $adminNote,
        ]);
        if ($sendEmail) {
            Mail::to($walletRechargeRequest->user->email)->send(
                new WalletRechargeRequestRejected($walletRechargeRequest)
        );
        }
    }
}

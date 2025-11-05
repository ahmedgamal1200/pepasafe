<?php

namespace App\Http\Controllers\Eventor\Wallet;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Models\PhoneNumber;
use App\Models\Plan;
use App\Services\RenewSubscriptionNow;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function eventorWallet()
    {
        $user = auth()->user()->load(['subscription.histories.subscription.plan']); // لو عندك علاقة اسمها subscription

        $paymentMethods = PaymentMethod::query()->get();

        $plans = Plan::query()->get();

        $phones = PhoneNumber::query()->get();

        return view('eventors.wallet', compact('user', 'paymentMethods', 'plans', 'phones'));
    }

    // التجديد التلقائي
    public function toggleAutoRenew(Request $request)
    {
        $request->validate([
            'auto_renew' => ['nullable', 'string'], // ممكن تخليها 'nullable' أو تشيلها خالص لو مش عايز تحقق
        ]);

        $user = Auth::user();
        $subscription = $user->subscription; // افترض أن دي علاقة صحيحة وموجودة في موديل User

        if (! $subscription) {
            return back()->with('error', trans_db('subscription.error'));
        }

        // هنا بنستخدم request->boolean() للتحويل
        $subscription->auto_renew = $request->boolean('auto_renew');

        $subscription->save();

        return back()->with('success', trans_db('subscription.success'));
    }

    public function renewNow(RenewSubscriptionNow $service): RedirectResponse
    {
        $result = $service->renewNow(Auth::user());

        return back()->with(is_bool($result) &&
        $result ? 'success' : 'error',
            is_string($result) ?
                $result : trans_db('subscription.renewed'));
    }
}

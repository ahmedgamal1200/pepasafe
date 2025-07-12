<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $guarded = ['id'];


    protected $casts = [
        'carry_over_credit' => 'boolean',
        'enable_attendance' => 'boolean',
        'enabled_channels' => 'array',

    ];

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function subscriptionHistories(): HasMany
    {
        return $this->hasMany(SubscriptionHistory::class);
    }

    public function paymentReceipts(): HasMany
    {
        return $this->hasMany(PaymentReceipt::class);
    }

    public function walletRechargeRequests(): HasMany
    {
        return $this->hasMany(WalletRechargeRequest::class);
    }



    public function getFeaturesListAttribute(): array
    {
        return collect(
            preg_match_all('/<li.*?>(.*?)<\/li>/s', $this->feature, $matches)
                ? $matches[1]
                : []
        )->map(function ($text){
            return html_entity_decode(strip_tags($text));
    })->toArray();
    }
}


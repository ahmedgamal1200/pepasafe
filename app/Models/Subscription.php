<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    protected $guarded = ['id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(SubscriptionHistory::class);
    }

    public function walletRechargeRequests(): HasMany
    {
        return $this->hasMany(WalletRechargeRequest::class);
    }
}

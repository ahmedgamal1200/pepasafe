<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletRechargeRequest extends Model
{
    protected $fillable = [
        'user_id',
        'plan_id',
        'amount',
        'receipt_path',
        'status',
        'admin_note',
//        'admin_id',
        'approved_at',
        'subscription_id',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

//    public function admin()
//    {
//        return $this->belongsTo(User::class, 'admin_id');
//    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionHistory extends Model
{
    protected $guarded = ['id'];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}

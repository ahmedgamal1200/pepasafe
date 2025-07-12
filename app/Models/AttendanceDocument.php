<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceDocument extends Model
{
    protected $guarded = ['id'];

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(Recipient::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(AttendanceTemplate::class);
    }
}

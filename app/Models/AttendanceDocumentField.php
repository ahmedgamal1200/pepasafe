<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceDocumentField extends Model
{

    protected $table = 'attendance_document_fields';

    protected $guarded = ['id'];

    public function template(): BelongsTo
    {
        return $this->belongsTo(AttendanceTemplate::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttendanceTemplate extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'send_via' => 'array',
        'send_at' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function templateFiles(): HasMany
    {
        return $this->hasMany(AttendanceTemplateFile::class);
    }

    public function fields(): HasMany
    {
        return $this->hasMany(AttendanceDocumentField::class);
    }

    public function excelUploads(): HasMany
    {
        return $this->hasMany(AttendanceExcelUpload::class);
    }

    public function attendanceDocuments(): HasMany
    {
        return $this->hasMany(AttendanceDocument::class);
    }
}

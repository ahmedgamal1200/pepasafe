<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentTemplate extends Model
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
        return $this->hasMany(TemplateFile::class, 'document_template_id');
    }

    public function fields(): HasMany
    {
        return $this->hasMany(DocumentField::class);
    }

    public function excelUploads(): HasMany
    {
        return $this->hasMany(ExcelUpload::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }
}

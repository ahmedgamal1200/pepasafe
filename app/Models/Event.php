<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Event extends Model
{
    protected $guarded = ['id'];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function excelUploads(): HasMany
    {
        return $this->hasMany(ExcelUpload::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function documentTemplates()
    {
        return $this->hasMany(DocumentTemplate::class);
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(Recipient::class);
    }

    protected static function booted(): void
    {
        static::creating(function ($event) {
            $event->slug = Str::slug($event->title.'-'.Str::random(4));
        });
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    protected $guarded = ['id'];

    public function excelUploads(): HasMany
    {
        return $this->hasMany(ExcelUpload::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

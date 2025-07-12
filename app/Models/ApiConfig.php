<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiConfig extends Model
{
    protected $table = 'api_configs'; // اسم الجدول في قاعدة البيانات

    protected $fillable = [
        'key',
        'value',
        'type',
        // 'description', // لو أضفت عمود الوصف
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $guarded = ['id'];

    public static function getValue(string $key): ?string
    {
        return static::where('key', $key)->value('value');
    }

}

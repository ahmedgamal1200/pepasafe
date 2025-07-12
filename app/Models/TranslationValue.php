<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TranslationValue extends Model
{
    protected $guarded = ['id'];

    public function key(): BelongsTo
    {
        return $this->belongsTo(TranslationKey::class, 'translation_key_id');
    }
}

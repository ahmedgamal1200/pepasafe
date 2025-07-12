<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TranslationKey extends Model
{
    protected $guarded = ['id'];

    public function values(): HasMany
    {
        return $this->hasMany(TranslationValue::class);
    }

    public function valueForLocale(string $locale)
    {
        return $this->values()
            ->firstWhere('locale', $locale)
            ?->value;
    }
}

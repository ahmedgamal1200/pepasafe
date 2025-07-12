<?php

use App\Models\TranslationKey;

if(!function_exists('trans_db')){
    function trans_db(string $key, string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        return TranslationKey::query()
            ->with('values')
            ->where('key', $key)
            ->first()
            ?->valueForLocale($locale) ?? $key;
    }
}

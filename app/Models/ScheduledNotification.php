<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class ScheduledNotification extends Model
{
    use HasTranslations;

    protected array $translatable = [
        'subject',
        'message',
    ];

    protected $guarded = ['id'];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'send_to_all' => 'boolean',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'id', 'user_ids');
    }

    protected function userIds(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => json_decode($value, true),
            set: fn (array $value) => json_encode($value),
        );
    }
}

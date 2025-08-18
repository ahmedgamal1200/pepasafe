<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'category_id',
        'profile_picture',
        'max_users',
        'slug',
        'qr_code',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function subscription(): HasOne
    {
        return $this->hasone(Subscription::class);
    }

    public function paymentReceipts(): HasMany
    {
        return $this->hasMany(PaymentReceipt::class);
    }

    // requests from users to recharge wallet
    public function walletRechargeRequests(): HasMany
    {
        return $this->hasMany(WalletRechargeRequest::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    // هاتلي كل ال doucs الي جاية من ال recipients اللي تبع اليوزر ده
    public function documents(): HasManyThrough
    {
        return $this->hasManyThrough(Document::class, Recipient::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }




//    // الطلبات اللي راجعها الأدمن
//    public function reviewedRechargeRequests()
//    {
//        return $this->hasMany(WalletRechargeRequestController::class, 'admin_id');
//    }

    // انشاء slug لكل المستخدمين
    protected static  function booted()
    {
        static::creating(function ($user) {
            $user->slug = Str::slug($user->name . '-' . Str::random(4));
        });
    }

}

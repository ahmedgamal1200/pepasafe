<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

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
        'is_attendance',
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

    // انشاء slug لكل المستخدمين
    protected static function booted(): void
    {
        static::creating(function ($user) {
            // هات آخر رقم مستخدم
            $lastNumber = User::withTrashed()
                ->selectRaw('MAX(CAST(SUBSTRING_INDEX(slug, "-", -1) AS UNSIGNED)) as max_number')
                ->value('max_number');

            $nextNumber = $lastNumber ? $lastNumber + 1 : 1000;

            // خليه ياخد الاسم + الرقم
            $user->slug = Str::slug($user->name) . '-' . $nextNumber;
        });
    }





}

<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasProfilePhoto, Notifiable, TwoFactorAuthenticatable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'google_id',
        'country',
        'password',
        'role',  // 'user' or 'admin'
        'status', // 'active', 'inactive', 'banned'
        'account_type', // 'free', 'premium'
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $appends = [
        'profile_photo_url',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            // NOTE: no 'password' => 'hashed' cast here — we handle it manually in the mutator below
        ];
    }

    // ─── MUTATORS ────────────────────────────────────────────────────────────
    // Auto-lowercase and trim email before saving
    protected function email(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => strtolower(trim($value)),
        );
    }

    // Auto-bcrypt password before saving (only if not already hashed)
    protected function password(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => is_null($value)
                ? null
                : ((str_starts_with($value, '$2y$') || str_starts_with($value, '$2a$')) ? $value : bcrypt($value)),
        );
    }

    // ─── HELPERS ─────────────────────────────────────────────────────────────
    // Quick check if this user is an admin
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    // ─── RELATIONSHIPS ───────────────────────────────────────────────────────
    // A user belongs to many groups via group_members
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_members')
                    ->withPivot('joined_at');
    }
}

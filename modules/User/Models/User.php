<?php

namespace Modules\User\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Modules\User\Traits\UserRoleScopes;
use Modules\User\Enums\UserRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, UserRoleScopes;

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'user_level' => UserRoles::class,
            'user_status' => 'boolean',
        ];
    }

    public function isActive(): bool
    {
        return $this->user_status;
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->user_status ? 'Active' : 'Inactive';
    }

    public function getUserLevelValueAttribute(): ?int
    {
        return $this->user_level?->value;
    }

    public function isSuperAdmin(): bool
    {
        return $this->user_level === UserRoles::SUPER_ADMIN;
    }

    public function isAdmin(): bool
    {
        return in_array($this->user_level, [
            UserRoles::SUPER_ADMIN,
            UserRoles::ADMIN,
        ]);
    }

    public function isCashier(): bool
    {
        return $this->user_level === UserRoles::CASHIER;
    }

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getInitialsAttribute(): string
    {
        return strtoupper(substr($this->first_name, 0, 1).substr($this->last_name, 0, 1));
    }

    public function getPhoneNumbersAttribute(): string
    {
        return $this->phone_number.''. $this->secondary_phone_number;
    }

    public function setEmailVerifiedAttribute($value)
    {
        if ($value) {
            $this->attributes['email_verified_at'] = now();
        } else {
            $this->attributes['email_verified_at'] = null;
        }
    }

    public function getEmailVerifiedAttribute(): bool
    {
        return !is_null($this->email_verified_at);
    }
}
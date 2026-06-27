<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'phone', 'is_active', 'location_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

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
            'is_active' => 'boolean',
        ];
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function staff()
    {
        return $this->hasOne(Staff::class);
    }

    public function notifications()
    {
        return $this->hasMany(AppNotification::class);
    }

    public function wasteDeposits()
    {
        return $this->hasMany(WasteDeposit::class);
    }

    public function wasteWithdrawals()
    {
        return $this->hasMany(WasteWithdrawal::class);
    }

    public function pushSubscriptions()
    {
        return $this->hasMany(PushSubscription::class);
    }

    public function hasPermission(string $permission): bool
    {
        $permissions = config("rbac.permissions.{$this->role}", []);

        return in_array('*', $permissions, true) || in_array($permission, $permissions, true);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function roleLabel(): string
    {
        return config("rbac.roles.{$this->role}", $this->role);
    }
}

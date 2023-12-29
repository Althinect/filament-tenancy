<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property Collection $organization
 */
class User extends Authenticatable implements FilamentUser, HasTenants
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function getTenants(Panel $panel): Collection
    {
        return $this->organization;
    }

    public function organization(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class);
    }

    public function canAccessTenant(Model $tenant): bool
    {
        return $this->organization->contains($tenant);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'model_has_roles', 'model_id', 'role_id');
    }

    public function isSuperAdmin(): bool
    {
        return in_array($this->email, explode(',', config('auth.super_admins')));
    }

    public function scopeNonSuperAdmins($query)
    {
        return $query->whereNotIn('email', explode(',', config('auth.super_admins')));
    }
}

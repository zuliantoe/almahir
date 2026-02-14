<?php

namespace App\Models;

use App\Models\Traits\HasRoles;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * User Model
 *
 * Represents a system user with RBAC capabilities.
 * Features:
 * - UUID as primary key
 * - Polymorphic relationship to Siswa/Guru/Staff via ref_id/ref_type
 * - Role-based access control via HasRoles trait
 * - Soft deletes for data preservation
 *
 * @property string $id UUID
 * @property string $username Unique login username
 * @property string $email Unique email address
 * @property string $name Display name
 * @property string|null $avatar Profile picture path
 * @property string|null $phone Phone number
 * @property string|null $ref_id Polymorphic reference ID
 * @property string|null $ref_type Polymorphic reference type
 * @property bool $is_active Account status
 *
 * @author SIAKAD Development Team
 */
class User extends Authenticatable
{
    use HasFactory, HasUuids, Notifiable, SoftDeletes, HasRoles;

    /**
     * The table associated with the model.
     */
    protected $table = 'sys_users';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'name',
        'avatar',
        'phone',
        'ref_id',
        'ref_type',
        'is_active',
        'last_login_at',
        'last_login_ip',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    /**
     * Get the polymorphically related entity (Siswa, Guru, Staff, etc.)
     *
     * This allows a user account to be linked to any entity type,
     * making it flexible for different user types in the academic system.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function ref()
    {
        return $this->morphTo('ref', 'ref_type', 'ref_id');
    }

    /**
     * Get the user's avatar URL.
     *
     * Returns a default avatar if none is set.
     *
     * @return string
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }

        // Return default avatar based on first letter of name
        $initial = strtoupper(substr($this->name ?? 'U', 0, 1));
        return "https://ui-avatars.com/api/?name={$initial}&color=fff&background=007bff";
    }

    /**
     * Update last login information.
     *
     * Call this method after successful authentication.
     *
     * @param string|null $ip IP address of the login request
     * @return void
     */
    public function recordLogin(?string $ip = null): void
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ip,
        ]);
    }

    /**
     * Scope: Filter active users only.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Filter users by role.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $roleName
     */
    public function scopeWithRole($query, string $roleName)
    {
        return $query->whereHas('roles', function ($q) use ($roleName) {
            $q->where('name', $roleName);
        });
    }

    /**
     * Get user's primary role display name.
     *
     * @return string|null
     */
    public function getPrimaryRoleAttribute(): ?string
    {
        $role = $this->roles->first();
        return $role ? $role->display_name : null;
    }
}

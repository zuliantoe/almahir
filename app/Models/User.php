<?php

namespace App\Models;

use App\Models\Traits\HasRoles;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\PegawaiManager\Models\Pegawai;

class User extends Authenticatable
{
    use HasFactory, HasUuids, Notifiable, SoftDeletes, HasRoles;

    protected $table = 'sys_users';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'username',
        'email',
        'password',
        'name',
        'avatar',
        'phone',
        'account_status',
        'last_login_at',
        'last_login_ip',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_login_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    // 🔥 1 User = 1 Pegawai
    public function pegawai(): HasOne
    {
        return $this->hasOne(Pegawai::class, 'user_id');
    }

    /**
     * Polymorphic relationship to link with Guru or Siswa.
     */
    public function ref()
    {
        return $this->morphTo('ref');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }

        $initial = strtoupper(substr($this->name ?? 'U', 0, 1));
        return "https://ui-avatars.com/api/?name={$initial}&color=fff&background=007bff";
    }

    public function getPrimaryRoleAttribute(): ?string
    {
        $role = $this->roles->first();
        return $role ? $role->display_name : null;
    }

    /*
    |--------------------------------------------------------------------------
    | METHODS
    |--------------------------------------------------------------------------
    */

    public function recordLogin(?string $ip = null): void
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ip,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('account_status', 'active');
    }

    public function scopeWithRole($query, string $roleName)
    {
        return $query->whereHas('roles', function ($q) use ($roleName) {
            $q->where('name', $roleName);
        });
    }
}

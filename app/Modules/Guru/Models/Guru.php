<?php

namespace Modules\Guru\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Guru Model
 * 
 * Represents a teacher in the academic system.
 */
class Guru extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'guru';

    protected $fillable = [
        'nip',
        'nama',
        'email',
        'tanggal_lahir',
        'tempat_lahir',
        'jenis_kelamin',
        'alamat',
        'telepon',
        'foto',
        'jabatan',
        'mata_pelajaran',
        'status',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    /**
     * Get the user account associated with this teacher.
     */
    public function user()
    {
        return $this->morphOne(User::class, 'ref');
    }

    /**
     * Scope: Filter active teachers only.
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    /**
     * Get the schedules associated with this teacher.
     */
    public function jadwalPelajaran()
    {
        return $this->hasMany(\App\Modules\Akademik\Models\JadwalPelajaran::class, 'guru_id');
    }
}

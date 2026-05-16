<?php

namespace Modules\PegawaiManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use Modules\Guru\Models\Guru;

class JabatanStruktural extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'jabatan_struktural';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'pegawai_id',
        'guru_id',
        'jenis_jabatan',
        'periode_mulai',
        'periode_selesai',
        'sk_pengangkatan',
        'ttd_digital',
        'stempel_jabatan',
        'status',
        'catatan',
    ];

    protected $casts = [
        'periode_mulai' => 'date',
        'periode_selesai' => 'date',
    ];

    /**
     * Relasi ke akun User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke profil Pegawai (jika pimpinan dari kalangan pegawai)
     */
    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    /**
     * Relasi ke profil Guru (jika pimpinan dari kalangan guru)
     */
    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    /**
     * Scope untuk mengambil jabatan yang masih aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }
}

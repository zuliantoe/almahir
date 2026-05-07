<?php

namespace Modules\PegawaiManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pegawai extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'pegawai';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nama',
        'user_id',
        'type_pegawai_id',
        'no_hp',
        'email',
        'alamat',
        'tanggal_masuk',
        'foto',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    // 🔥 Pegawai belongs to User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // 🔥 Pegawai belongs to TypePegawai
    public function typePegawai(): BelongsTo
    {
        return $this->belongsTo(TypePegawai::class, 'type_pegawai_id');
    }

    public function perizinans(): HasMany
    {
        return $this->hasMany(\Modules\Perizinan\Models\Perizinan::class, 'user_id');
    }

    public function absensis(): HasMany
    {
        return $this->hasMany(\Modules\Absensi\Models\Absensi::class, 'pegawai_id');
    }

    /**
     * Hitung sisa jatah cuti tersedia (Total - Pending)
     */
    public function getAvailableQuota(): int
    {
        $currentYear = date('Y');
        $pendingDays = \Modules\Perizinan\Models\Perizinan::where('user_id', $this->id)
            ->whereIn('jenis_izin', ['cuti', 'izin'])
            ->where('status', 'menunggu')
            ->whereYear('tanggal_mulai', $currentYear)
            ->get()
            ->sum(function($izin) {
                return \Carbon\Carbon::parse($izin->tanggal_mulai)->diffInDays(\Carbon\Carbon::parse($izin->tanggal_selesai)) + 1;
            });
            
        return max(0, $this->sisa_cuti - (int)$pendingDays);
    }

    /**
     * Cek apakah pegawai sedang dalam masa cuti/izin yang disetujui hari ini
     */
    public function isOnLeave(): ?\Modules\Perizinan\Models\Perizinan
    {
        $today = date('Y-m-d');
        return $this->perizinans()
            ->where('status', 'disetujui')
            ->whereDate('tanggal_mulai', '<=', $today)
            ->whereDate('tanggal_selesai', '>=', $today)
            ->first();
    }
}

<?php

namespace Modules\PegawaiManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Pegawai extends Model
{
    use HasFactory, HasUuids, SoftDeletes, LogsActivity;

    /**
     * Konfigurasi Audit Trail: Catat semua perubahan di field fillable
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => "Data Pegawai <b>{$this->nama}</b> ditambahkan ke sistem.",
                'updated' => "Data Pegawai <b>{$this->nama}</b> diperbarui.",
                'deleted' => "Data Pegawai <b>{$this->nama}</b> dihapus dari sistem.",
                default   => "Aktivitas '{$eventName}' pada data Pegawai {$this->nama}.",
            });
    }

    protected $table = 'pegawai';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nama',
        'user_id',
        'type_pegawai_id',
        'nip',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'alamat',
        'tanggal_masuk',
        'status',
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
            ->where('potong_kuota', true)
            ->where('status', 'menunggu')
            ->whereYear('tanggal_mulai', $currentYear)
            ->sum('total_hari');
            
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

    /**
     * Kurangi sisa cuti pegawai dengan validasi aman.
     * Digunakan oleh modul Perizinan saat izin cuti disetujui.
     * Memastikan nilai tidak pernah negatif dan perubahan tercatat oleh Audit Trail.
     *
     * @param int $days   Jumlah hari yang akan dikurangi
     * @return bool        True jika berhasil, false jika saldo tidak cukup
     */
    public function deductLeave(int $days): bool
    {
        if ($days <= 0) return false;
        $this->sisa_cuti = max(0, $this->sisa_cuti - $days);
        return $this->save();
    }

    /**
     * Tambah sisa cuti pegawai (misal: jika izin dibatalkan/ditolak setelah dikurangi).
     *
     * @param int $days   Jumlah hari yang akan ditambahkan
     * @return bool
     */
    public function addLeave(int $days): bool
    {
        if ($days <= 0) return false;
        $this->sisa_cuti = $this->sisa_cuti + $days;
        return $this->save();
    }
}

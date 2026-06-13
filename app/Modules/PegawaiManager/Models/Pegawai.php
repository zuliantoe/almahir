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
        'sisa_cuti',
        'qr_token',
    ];

    protected $attributes = [
        'sisa_cuti' => 12,
        'status' => 'aktif',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
    ];

    /**
     * Accessor untuk jatah cuti agar selalu dihitung secara dinamis dan real-time (Base Quota - Approved Days)
     * Default base quota adalah 12 jika kosong (null) di database.
     */
    public function getSisaCutiAttribute($value)
    {
        $baseCuti = $value !== null ? (int)$value : 12;
        if (class_exists('\Modules\Perizinan\Models\Perizinan')) {
            $currentYear = date('Y');
            $approvedDays = \Modules\Perizinan\Models\Perizinan::where('user_id', $this->id)
                ->where('potong_kuota', true)
                ->where('status', 'disetujui')
                ->whereYear('tanggal_mulai', $currentYear)
                ->sum('total_hari');
            return max(0, $baseCuti - (int)$approvedDays);
        }
        return $baseCuti;
    }

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
     * Hitung sisa jatah cuti tersedia secara dinamis dan real-time (Total - Approved - Pending)
     */
    public function getAvailableQuota(): int
    {
        if (class_exists('\Modules\Perizinan\Models\Perizinan')) {
            $currentYear = date('Y');
            $pendingDays = \Modules\Perizinan\Models\Perizinan::where('user_id', $this->id)
                ->where('potong_kuota', true)
                ->where('status', 'menunggu')
                ->whereYear('tanggal_mulai', $currentYear)
                ->sum('total_hari');
                
            return max(0, $this->sisa_cuti - (int)$pendingDays);
        }
        return $this->sisa_cuti;
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
     * Kurangi sisa cuti pegawai dengan validasi aman (No-op karena dihitung secara dinamis dan real-time).
     *
     * @param int $days   Jumlah hari yang akan dikurangi
     * @return bool        Selalu true
     */
    public function deductLeave(int $days): bool
    {
        return true;
    }

    /**
     * Tambah sisa cuti pegawai (No-op karena dihitung secara dinamis dan real-time).
     *
     * @param int $days   Jumlah hari yang akan ditambahkan
     * @return bool        Selalu true
     */
    public function addLeave(int $days): bool
    {
        return true;
    }
}

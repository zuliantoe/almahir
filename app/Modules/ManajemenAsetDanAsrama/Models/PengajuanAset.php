<?php

namespace App\Modules\ManajemenAsetDanAsrama\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class PengajuanAset extends Model
{
    use SoftDeletes;

    protected $table = 'pengajuan_aset';

    protected $fillable = [
        'nomor_pengajuan',
        'nama_aset',
        'deskripsi_pengajuan',
        'estimasi_harga',
        'tanggal_pengajuan',
        'pengaju_id',
        'status',
        'catatan_tolak',
        'alasan_pengajuan_ulang',
        'approved_by',
        'approved_at',
        'deleted_by',
        'alasan_hapus',
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'date',
        'approved_at' => 'datetime',
        'estimasi_harga' => 'decimal:2',
    ];

    /**
     * Relasi ke user yang mengajukan
     */
    public function pengaju()
    {
        return $this->belongsTo(User::class, 'pengaju_id');
    }

    /**
     * Relasi ke user yang menyetujui
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Relasi ke pengadaan (1 pengajuan bisa jadi 1 pengadaan)
     */
    public function pengadaan()
    {
        return $this->hasOne(PengadaanAset::class, 'pengajuan_id');
    }

    /**
     * Relasi ke user yang menghapus
     */
    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Scope untuk status diajukan
     */
    public function scopeDiajukan($query)
    {
        return $query->where('status', 'diajukan');
    }

    /**
     * Scope untuk status disetujui
     */
    public function scopeDisetujui($query)
    {
        return $query->where('status', 'disetujui');
    }

    /**
     * Scope untuk status ditolak
     */
    public function scopeDitolak($query)
    {
        return $query->where('status', 'ditolak');
    }

    /**
     * Scope untuk status proses pengadaan
     */
    public function scopeProsesPengadaan($query)
    {
        return $query->where('status', 'proses_pengadaan');
    }

    /**
     * Scope untuk yang aktif (belum dihapus)
     */
    public function scopeAktif($query)
    {
        return $query->whereNull('deleted_at');
    }
}
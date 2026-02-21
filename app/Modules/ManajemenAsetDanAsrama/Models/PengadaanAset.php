<?php

namespace App\Modules\ManajemenAsetDanAsrama\Models;

use Illuminate\Database\Eloquent\Model;

class PengadaanAset extends Model
{
    protected $table = 'pengadaan_aset';

    protected $fillable = [
        'nomor_po',
        'pengajuan_id',
        'vendor',
        'tanggal_pesan',
        'estimasi_datang',
        'tanggal_datang',
        'biaya_riil',
        'catatan_pengadaan',
        'status',
    ];

    protected $casts = [
        'tanggal_pesan' => 'date',
        'estimasi_datang' => 'date',
        'tanggal_datang' => 'date',
        'biaya_riil' => 'decimal:2',
    ];

    /**
     * Relasi ke pengajuan aset
     */
    public function pengajuan()
    {
        return $this->belongsTo(PengajuanAset::class, 'pengajuan_id');
    }

    /**
     * Relasi ke aset yang dihasilkan
     */
    public function aset()
    {
        return $this->hasOne(Aset::class, 'pengadaan_id');
    }

    /**
     * Scope untuk status dipesan
     */
    public function scopeDipesan($query)
    {
        return $query->where('status', 'dipesan');
    }

    /**
     * Scope untuk status sudah datang
     */
    public function scopeDatang($query)
    {
        return $query->where('status', 'datang');
    }

    /**
     * Scope untuk status batal
     */
    public function scopeBatal($query)
    {
        return $query->where('status', 'batal');
    }
}
<?php

namespace App\Modules\ManajemenAsetDanAsrama\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User; // ✅ TAMBAHKAN INI - Import User model
use Modules\Siswa\Models\Siswa;  // Gunakan Modules\Siswa...

class Aset extends Model
{
    use SoftDeletes;

    protected $table = 'aset';

    protected $fillable = [
        'kode_aset',
        'nama_aset',
        'tanggal_pengajuan',
        'harga',
        'status_kondisi',
        'tanggal_pengadaan',
        'kondisi',
        'deskripsi_aset',
        'pengadaan_id',
        'kamar_id',
        'deleted_by',
        'alasan_hapus',
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'date',
        'tanggal_pengadaan' => 'date',
        'harga' => 'decimal:2',
    ];

    /**
     * Relasi: 1 aset punya banyak kerusakan
     */
    public function kerusakan()
    {
        return $this->hasMany(Kerusakan::class, 'aset_id');
    }

    /**
     * Relasi: 1 aset punya banyak pemeliharaan
     */
    public function pemeliharaan()
    {
        return $this->hasMany(Pemeliharaan::class, 'aset_id');
    }

    /**
     * Get the pengadaan that owns the aset.
     */
    public function pengadaan()
    {
        return $this->belongsTo(PengadaanAset::class, 'pengadaan_id');
    }

    /**
     * Get the kamar that owns the aset.
     */
    public function kamar()
    {
        return $this->belongsTo(Kamar::class, 'kamar_id');
    }

    /**
     * Relasi ke user yang menghapus (soft delete)
     */
    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Accessor: Status Badge HTML
     */
    public function getStatusBadgeAttribute(): string
    {
        $status = $this->status_kondisi;
        $badges = [
            'baik'              => '<span class="badge badge-success">Baik</span>',
            'rusak'             => '<span class="badge badge-danger">Rusak</span>',
            'dalam_perbaikan'   => '<span class="badge badge-warning">Dalam Perbaikan</span>',
            'sudah_diperbaiki'  => '<span class="badge badge-info">Sudah Diperbaiki</span>',
        ];

        return $badges[$status] ?? '<span class="badge badge-secondary">'.ucfirst($status).'</span>';
    }

    /**
     * Accessor: Harga Terformat Rp
     */
    public function getHargaFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }

    /**
     * Scope untuk filter berdasarkan status kondisi
     */
    public function scopeStatusKondisi($query, $status)
    {
        return $query->where('status_kondisi', $status);
    }

    /**
     * Scope untuk aset yang baik
     */
    public function scopeBaik($query)
    {
        return $query->where('status_kondisi', 'baik');
    }

    /**
     * Scope untuk aset yang rusak
     */
    public function scopeRusak($query)
    {
        return $query->where('status_kondisi', 'rusak');
    }

    /**
     * Scope untuk aset dalam perbaikan
     */
    public function scopeDalamPerbaikan($query)
    {
        return $query->where('status_kondisi', 'dalam_perbaikan');
    }

    /**
     * Scope untuk aset sudah diperbaiki
     */
    public function scopeSudahDiperbaiki($query)
    {
        return $query->where('status_kondisi', 'sudah_diperbaiki');
    }

    /**
     * Scope untuk aset yang belum dihapus (dengan soft delete)
     */
    public function scopeAktif($query)
    {
        return $query->whereNull('deleted_at');
    }

    /**
     * Scope untuk aset yang sudah dihapus (trash)
     */
    public function scopeTerhapus($query)
    {
        return $query->onlyTrashed();
    }
}
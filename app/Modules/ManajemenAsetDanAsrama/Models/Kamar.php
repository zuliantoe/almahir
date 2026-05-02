<?php

namespace Modules\ManajemenAsetDanAsrama\Models;  // SUDAH BENAR

use Illuminate\Database\Eloquent\Model;

class Kamar extends Model
{
    protected $table = 'kamar';

    protected $fillable = [
        'nama_kamar',
        'kapasitas',
        'deskripsi',
    ];

    // Relasi: 1 kamar punya banyak penghuni
    public function penghuni()
    {
        return $this->hasMany(KamarPenghuni::class, 'kamar_id');
    }

    /**
     * Accessor: Jumlah Terisi
     */
    public function getTerisiAttribute(): int
    {
        return $this->penghuni()->count();
    }

    /**
     * Accessor: Sisa Slot
     */
    public function getSisaAttribute(): int
    {
        return max(0, $this->kapasitas - $this->terisi);
    }

    /**
     * Accessor: Status Kapasitas Badge
     */
    public function getStatusKapasitasBadgeAttribute(): string
    {
        $terisi = $this->terisi;
        $kapasitas = $this->kapasitas;

        if ($terisi >= $kapasitas) {
            return '<span class="badge badge-danger">Penuh</span>';
        }

        return '<span class="badge badge-success">Tersedia (' . ($kapasitas - $terisi) . ' slot)</span>';
    }
}

<?php

namespace App\Modules\ManajemenAsetDanAsrama\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Siswa\Models\Siswa;

class KamarPenghuni extends Model
{
    protected $table = 'kamar_penghuni';

    protected $fillable = [
        'kamar_id',
        'siswa_id',
        'tanggal_masuk',
        'tanggal_keluar',
        'keterangan',
        'periode_start',
        'periode_end',
        'jabatan',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'tanggal_keluar' => 'date',
    ];

    public function kamar()
    {
        return $this->belongsTo(Kamar::class, 'kamar_id');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id', 'id')->withTrashed();
    }

    /**
     * Scope: Hanya penghuni yang masih aktif (belum checkout).
     */
    public function scopeAktif($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('tanggal_keluar')
              ->orWhere('tanggal_keluar', '>', now());
        });
    }
}

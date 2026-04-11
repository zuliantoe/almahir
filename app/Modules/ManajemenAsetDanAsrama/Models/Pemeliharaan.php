<?php

namespace Modules\ManajemenAsetDanAsrama\Models;

use Illuminate\Database\Eloquent\Model;

class Pemeliharaan extends Model
{
    protected $table = 'pemeliharaan';

    protected $fillable = [
        'aset_id',
        'tanggal_mulai_pemeliharaan',
        'tanggal_selesai_pemeliharaan',
        'tanggal_pemeliharaan',
        'deskripsi_pemeliharaan',
        'biaya_pemeliharaan',
        'biaya',
        'catatan',
        'status',
        'catatan_selesai',
    ];

    protected $casts = [
        'tanggal_mulai_pemeliharaan' => 'date',
        'tanggal_selesai_pemeliharaan' => 'date',
        'tanggal_pemeliharaan' => 'date',
        'biaya_pemeliharaan' => 'decimal:2',
        'biaya' => 'decimal:2',
    ];

    // Relasi: pemeliharaan milik 1 aset
    public function aset()
    {
        return $this->belongsTo(Aset::class, 'aset_id');
    }
}

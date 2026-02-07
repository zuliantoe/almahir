<?php

namespace App\Modules\ManajemenAsetDanAsrama\Models;

use Illuminate\Database\Eloquent\Model;

class Pemeliharaan extends Model
{
    protected $table = 'pemeliharaan';

    protected $fillable = [
        'aset_id',
        'tanggal_mulai_pemeliharaan',
        'tanggal_selesai_pemeliharaan',
        'deskripsi_pemeliharaan',
        'biaya_pemeliharaan',
    ];

    // Relasi: pemeliharaan milik 1 aset
    public function aset()
    {
        return $this->belongsTo(Aset::class, 'aset_id');
    }
}

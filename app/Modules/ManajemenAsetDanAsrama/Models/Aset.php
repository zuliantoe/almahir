<?php

namespace App\Modules\ManajemenAsetDanAsrama\Models;

use Illuminate\Database\Eloquent\Model;

class Aset extends Model
{
    protected $table = 'aset';

    protected $fillable = [
        'kode_aset',
        'nama_aset',
        'tanggal_pengajuan',
        'harga',
        'status',
        'tanggal_pengadaan',
        'kondisi',
        'deskripsi_aset',
    ];

    // Relasi: 1 aset punya banyak kerusakan
    public function kerusakan()
    {
        return $this->hasMany(Kerusakan::class, 'aset_id');
    }

    // Relasi: 1 aset punya banyak pemeliharaan
    public function pemeliharaan()
    {
        return $this->hasMany(Pemeliharaan::class, 'aset_id');
    }
}

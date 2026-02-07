<?php

namespace Modules\ManajemenAsetDanAsrama\Models;

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
}

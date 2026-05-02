<?php

namespace App\Modules\ManajemenAsetDanAsrama\Models;

use Illuminate\Database\Eloquent\Model;

class Kerusakan extends Model
{
    protected $table = 'kerusakan';

    protected $fillable = [
        'aset_id',
        'tanggal_rusak',
        'tanggal_kerusakan',
        'deskripsi_kerusakan',
        'tingkat_kerusakan',
        'status_penanganan',
        'catatan',
    ];

    protected $casts = [
        'tanggal_rusak' => 'date',
        'tanggal_kerusakan' => 'date',
    ];

    // Relasi: kerusakan milik 1 aset
    public function aset()
    {
        return $this->belongsTo(Aset::class, 'aset_id');
    }
}

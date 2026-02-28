<?php

namespace Modules\ManajemenAsetDanAsrama\Models;  // SUDAH BENAR

use Illuminate\Database\Eloquent\Model;
use Modules\Siswa\Models\Siswa;  // Gunakan Modules\Siswa...

class KamarPenghuni extends Model
{
    protected $table = 'kamar_penghuni';

    protected $fillable = [
        'kamar_id',
        'siswa_id',
        'periode_start',
        'periode_end',
        'jabatan',
    ];

    public function kamar()
    {
        return $this->belongsTo(Kamar::class, 'kamar_id');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id', 'id');
    }
}

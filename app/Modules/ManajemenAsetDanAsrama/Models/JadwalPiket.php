<?php

namespace Modules\ManajemenAsetDanAsrama\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Siswa\Models\Siswa;

class JadwalPiket extends Model
{
    protected $table = 'jadwal_piket';

    protected $fillable = [
        'bulan',
        'pekan',
        'hari',
        'tempat',
        'siswa_id',
        'status',
    ];

    // Relasi: jadwal piket milik 1 siswa
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id', 'id');
    }
}

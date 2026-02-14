<?php

namespace Modules\PenilaianDanPresensi\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Presensi extends Model
{
    use HasFactory;

    protected $table = 'presensi';

    protected $fillable = [
        'id_siswa',
        'id_guru',
        'id_mapel',
        'id_jadwal_pelajaran',
        'jam',
        'status',
        'kategori',
    ];

    protected $casts = [
        'jam' => 'datetime:H:i',
    ];

    // Relationships
    public function siswa()
    {
        return $this->belongsTo(\App\Models\Siswa::class, 'id_siswa');
    }

    public function guru()
    {
        return $this->belongsTo(\App\Models\Guru::class, 'id_guru');
    }
}

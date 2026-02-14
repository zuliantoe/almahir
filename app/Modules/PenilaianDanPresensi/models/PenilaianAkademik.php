<?php

namespace Modules\PenilaianDanPresensi\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Guru\Models\Guru;
use Modules\Siswa\Models\Siswa;

class PenilaianAkademik extends Model
{
    use HasFactory;

    protected $table = 'penilaian';

    protected $fillable = [
        'id_siswa',
        'id_guru',
        'id_mapel',
        'id_tahun_ajaran',
        'nilai',
    ];

    protected $casts = [
        'nilai' => 'integer',
    ];

    // Relationships
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'id_guru');
    }
}

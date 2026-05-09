<?php

namespace Modules\PenilaianDanPresensi\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Guru\Models\Guru;
use Modules\Siswa\Models\Siswa;
use App\Modules\Akademik\Models\MataPelajaran;
use App\Modules\Akademik\Models\JadwalPelajaran;

class Presensi extends Model
{
    use HasFactory;

    protected $table = 'presensi';

    protected $fillable = [
        'siswa_id',
        'guru_id',
        'mapel_id',
        'jadwal_pelajaran_id',
        'tahunajaran_id',
        'semester',
        'author_id',
        'jam',
        'status',
        'kategori',
        'scan_id',
    ];

    protected $casts = [
        'jam' => 'datetime:H:i',
    ];

    // Relationships
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class, 'mapel_id');
    }

    public function jadwalPelajaran()
    {
        return $this->belongsTo(JadwalPelajaran::class, 'jadwal_pelajaran_id');
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(\App\Modules\Akademik\Models\TahunAjaran::class, 'tahunajaran_id');
    }

    public function author()
    {
        return $this->belongsTo(\App\Models\User::class, 'author_id');
    }
}

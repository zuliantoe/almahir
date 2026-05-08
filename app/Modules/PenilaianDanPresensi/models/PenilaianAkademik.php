<?php

namespace Modules\PenilaianDanPresensi\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Guru\Models\Guru;
use Modules\Siswa\Models\Siswa;
use App\Modules\Akademik\Models\MataPelajaran;
use App\Modules\Akademik\Models\TahunAjaran;

class PenilaianAkademik extends Model
{
    use HasFactory;

    protected $table = 'penilaian';

    protected $fillable = [
        'siswa_id',
        'guru_id',
        'mapel_id',
        'tahunajaran_id',
        'jenis_nilai',
        'semester',
        'author_id',
        'nilai',
        'kkm',
    ];

    protected $casts = [
        'nilai' => 'integer',
        'kkm' => 'integer',
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

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahunajaran_id');
    }

    public function author()
    {
        return $this->belongsTo(\App\Models\User::class, 'author_id');
    }
}
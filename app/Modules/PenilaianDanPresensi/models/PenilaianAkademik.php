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
        'id_siswa',
        'id_guru',
        'id_mapel',
        'id_tahun_ajaran',
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
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'id_guru');
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class, 'id_mapel');
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'id_tahun_ajaran');
    }
}
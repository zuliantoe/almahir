<?php

namespace Modules\PenilaianDanPresensi\Models;

use App\Modules\Akademik\Models\Kelas as AkademikKelas;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Guru\Models\Guru;
use Modules\Siswa\Models\Siswa;

class PenilaianTahfidz extends Model
{
    use HasFactory;

    protected $table = 'penilaian_tahfidz';

    protected $fillable = [
        'siswa_id',
        'kelas_id',
        'rombel_id',
        'guru_id',
        'tahunajaran_id',
        'semester',
        'author_id',
        'tanggal',
        'surat_awal',
        'surat_akhir',
        'ayat_awal',
        'ayat_akhir',
        'nilai',
        'status_capaian',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'ayat_awal' => 'integer',
        'ayat_akhir' => 'integer',
        'nilai' => 'integer',
    ];

    // Relationships
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function rombel()
    {
        return $this->belongsTo(\App\Modules\Akademik\Models\Rombel::class, 'rombel_id');
    }

    public function kelas()
    {
        return $this->belongsTo(AkademikKelas::class, 'kelas_id');
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'guru_id');
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

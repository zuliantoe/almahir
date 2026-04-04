<?php

namespace Modules\PenilaianDanPresensi\Models;

use App\Modules\Akademik\Models\kelas as AkademikKelas;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Guru\Models\Guru;
use Modules\Siswa\Models\Siswa;

class PenilaianTahfidz extends Model
{
    use HasFactory;

    protected $table = 'penilaian_tahfidz';

    protected $fillable = [
        'id_siswa',
        'id_kelas',
        'tanggal',
        'surat_awal',
        'surat_akhir',
        'ayat_awal',
        'ayat_akhir',
        'id_guru',
        'nilai',
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
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }

    public function kelas()
    {
        return $this->belongsTo(AkademikKelas::class, 'id_kelas');
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'id_guru');
    }
}

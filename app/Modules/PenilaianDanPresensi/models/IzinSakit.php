<?php

namespace Modules\PenilaianDanPresensi\Models;

use App\Modules\Akademik\Models\kelas as AkademikKelas;
use App\Modules\Akademik\Models\MataPelajaran;
use App\Modules\Akademik\Models\JadwalPelajaran;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Siswa\Models\Siswa;

class IzinSakit extends Model
{
    use HasFactory;

    protected $table = 'izin_sakit';

    protected $fillable = [
        'siswa_id',
        'kelas_id',
        'mapel_id',
        'jadwal_pelajaran_id',
        'tahunajaran_id',
        'semester',
        'author_id',
        'tgl_mulai',
        'tgl_selesai',
        'jenis',
        'tipe_izin',
        'keterangan',
        'bukti_foto',
        'status',
        'konfirmasi_oleh',
        'waktu_konfirmasi',
    ];

    protected $casts = [
        'tgl_mulai' => 'date',
        'tgl_selesai' => 'date',
        'waktu_konfirmasi' => 'datetime',
    ];

    // Relationships
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function rombel()
    {
        return $this->belongsTo(AkademikKelas::class, 'kelas_id');
    }

    // Alias for backward compatibility
    public function kelas()
    {
        return $this->rombel();
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

    public function konfirmasiOleh()
    {
        return $this->belongsTo(\App\Models\User::class, 'konfirmasi_oleh');
    }
}

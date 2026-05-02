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
        'id_siswa',
        'id_kelas',
        'jenis',
        'tipe_izin',
        'id_mapel',
        'id_jadwal_pelajaran',
        'tgl_mulai',
        'tgl_selesai',
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
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }

    public function kelas()
    {
        return $this->belongsTo(AkademikKelas::class, 'id_kelas');
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class, 'id_mapel');
    }

    public function jadwalPelajaran()
    {
        return $this->belongsTo(JadwalPelajaran::class, 'id_jadwal_pelajaran');
    }

    public function konfirmasiOleh()
    {
        return $this->belongsTo(\App\Models\User::class, 'konfirmasi_oleh');
    }
}

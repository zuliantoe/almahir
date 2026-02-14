<?php

namespace Modules\PenilaianDanPresensi\Models;

use App\Modules\Akademik\Models\kelas;
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
        'tgl_mulai',
        'tgl_selesai',
    ];

    protected $casts = [
        'tgl_mulai' => 'date',
        'tgl_selesai' => 'date',
    ];

    // Relationships
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }

    public function kelas()
    {
        return $this->belongsTo(kelas::class, 'id_kelas');
    }
}

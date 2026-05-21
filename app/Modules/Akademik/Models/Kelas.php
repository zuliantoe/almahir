<?php

namespace App\Modules\Akademik\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Guru\Models\Guru;


class Kelas extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kelas';

    protected $fillable = [
        'kode_kelas',
        'nama_kelas',
        'tingkat_id',
        'kapasitas',
    ];

    public function tingkat()
    {
        return $this->belongsTo(Tingkat::class, 'tingkat_id');
    }


    public function rombel()
    {
        return $this->hasMany(Rombel::class, 'kelas_id');
    }

    public function kurikulum()
    {
        return $this->hasMany(Kurikulum::class, 'kelas_id');
    }

    public function jadwalPelajaran()
    {
        // Based on the latest ERD, jadwal_pelajaran belongs to Rombel, 
        // but if we want to get them through Kelas, we use hasManyThrough
        return $this->hasManyThrough(JadwalPelajaran::class, Rombel::class, 'kelas_id', 'rombel_id');
    }
}

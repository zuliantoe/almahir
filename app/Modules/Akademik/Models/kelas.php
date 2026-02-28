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
        'namakelas',
        'jenjang',
        'guru_id',
    ];

    public function walikelas()
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    public function jadwalPelajaran()
    {
        return $this->hasMany(JadwalPelajaran::class, 'kelas_id');
    }

    public function kurikulum()
    {
        return $this->hasMany(Kurikulum::class, 'kelas_id');
    }
}

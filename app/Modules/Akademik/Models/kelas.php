<?php

namespace App\Modules\Akademik\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Siswa\Models\Siswa;

class kelas extends Model
{
    use HasFactory;

    protected $table='kelas';
    protected $fillable = [
        'nama_kelas',
        'kode_kelas',
        'jenjang',
        'guru_id',
        'kapasitas',
        'keterangan',
        'status'
    ];

    public function walikelas():BelongsTo
    {
        return $this->belongsTo(Guru::class,'guru_id');
    }


    public function jadwalPelajaran():HasMany
    {
        return $this->hasMany(JadwalPelajaran::class,'kelas_id');
    }

    public function kurikulum():HasMany
    {
        return $this->hasMany(Kurikulum::class,'kelas_id');
    }

    public function romberl():HasMany
    {
        return $this->hasMany(Siswa::class,Rombel::class,'kelas_id','id','siswa_id');
    }

    public function scopeAktif($query)
    {
        return $query->where('status','aktif');
    }
}

<?php

namespace App\Modules\Akademik\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Guru\Models\Guru;

class Rombel extends Model
{
    use HasFactory;

    protected $table ='rombel';
    
    protected $fillable = [
        'nama_rombel',
        'kelas_id',
        'tahunajaran_id',
        'guru_id',
        'keterangan'
    ];

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class,'tahunajaran_id');
    }

    public function walikelas(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    public function riwayatSiswa(): HasMany
    {
        return $this->hasMany(RombelSiswa::class, 'rombel_id');
    }

    public function siswa()
    {
        return $this->belongsToMany(\Modules\Siswa\Models\Siswa::class, 'rombel_siswa', 'rombel_id', 'siswa_id')
            ->withPivot('status')
            ->withTimestamps();
    }

    public function aktifSiswa()
    {
        return $this->siswa()->wherePivot('status', 'aktif');
    }

    public function jadwalPelajaran(): HasMany
    {
        return $this->hasMany(JadwalPelajaran::class, 'rombel_id');
    }
}

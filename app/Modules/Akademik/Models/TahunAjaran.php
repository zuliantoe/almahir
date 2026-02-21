<?php

namespace App\Modules\Akademik\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TahunAjaran extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'tahun_ajaran';
    protected $fillable = ['tahunajaran', 'status'];

    public function kalenderAkademik(): HasMany
    {
        return $this->hasMany(KalenderAkademik::class, 'tahunajaran_id');
    }

    public function jadwalPelajaran(): HasMany
    {
        return $this->hasMany(JadwalPelajaran::class, 'tahunajaran_id');
    }

    public function kurikulum(): HasMany
    {
        return $this->hasMany(Kurikulum::class, 'tahunajaran_id');
    }

    public function rombel(): HasMany
    {
        return $this->hasMany(Rombel::class, 'tahunajaran_id');
    }

    public function scopeAktif($query)
    {
        return $query->where('status', true);
    }
}

<?php

namespace App\Modules\Akademik\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $tahunajaran
 * @property string $semester
 * @property bool $status
 * @property string|null $keterangan
 */
class TahunAjaran extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tahun_ajaran';
    protected $fillable = ['tahunajaran', 'semester', 'status', 'keterangan'];

    public function kalenderAkademik(): HasMany
    {
        return $this->hasMany(KalenderAkademik::class, 'tahunajaran_id');
    }

    public function jadwalPelajaran()
    {
        return $this->hasManyThrough(
            JadwalPelajaran::class,
            Rombel::class,
            'tahunajaran_id', // Foreign key on rombel table
            'rombel_id',      // Foreign key on jadwal_pelajaran table
            'id',             // Local key on tahun_ajaran table
            'id'              // Local key on rombel table
        );
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

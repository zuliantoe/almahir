<?php

namespace App\Modules\Akademik\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MataPelajaran extends Model
{
    use SoftDeletes;

    protected $table = 'mata_pelajaran';

    protected $fillable = [
        'kode',
        'nama',        
        'kategori_id',
        'kelompok',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriPelajaran::class, 'kategori_id');
    }

    public function jadwalPelajaran()
    {
        return $this->hasMany(JadwalPelajaran::class, 'mapel_id');
    }

    public function kurikulum()
    {
        return $this->hasMany(Kurikulum::class, 'mapel_id');
    }
}

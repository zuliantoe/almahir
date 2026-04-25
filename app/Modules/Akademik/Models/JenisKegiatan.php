<?php

namespace App\Modules\Akademik\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisKegiatan extends Model
{
    use HasFactory;

    protected $table = 'jenis_kegiatan';
    protected $fillable = ['jeniskegiatan','deskripsi'];

    public function kalenderAkademik():HasMany
    {
        return $this->hasMany(KalenderAkademik::class, 'kegiatan_id');
    }
}

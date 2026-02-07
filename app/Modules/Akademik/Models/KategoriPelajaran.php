<?php

namespace App\Modules\Akademik\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriPelajaran extends Model
{
    //
    protected $table = 'kategori_pelajaran';
    protected $fillable = ['nama'];

    public function mataPelajaran():HasMany
    {
        return $this->hasMany(MataPelajaran::class,'kategori_id');
    }
}

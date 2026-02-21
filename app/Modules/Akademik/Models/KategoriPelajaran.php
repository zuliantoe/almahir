<?php

namespace App\Modules\Akademik\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class KategoriPelajaran extends Model
{
    use SoftDeletes;
    //
    protected $table = 'kategori_pelajaran';
    protected $fillable = ['kategori'];

    public function mataPelajaran():HasMany
    {
        return $this->hasMany(MataPelajaran::class,'kategori_id');
    }
}

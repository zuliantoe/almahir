<?php

namespace App\Modules\Akademik\Models;

use Illuminate\Database\Eloquent\Model;

class MataPelajaran extends Model
{
    protected $table = 'mata_pelajaran';
    protected $fillable = [
        'kode',
        'name',
        'kategori_id',
    ];
}

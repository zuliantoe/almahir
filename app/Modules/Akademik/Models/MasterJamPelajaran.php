<?php

namespace App\Modules\Akademik\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterJamPelajaran extends Model
{
    use HasFactory;

    protected $table = 'master_jam_pelajarans';

    protected $fillable = [
        'hari',
        'jamke',
        'jamawal',
        'jamakhir',
        'is_istirahat',
    ];

    protected $casts = [
        'is_istirahat' => 'boolean',
    ];
}


<?php

namespace App\Modules\Akademik\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tingkat extends Model
{
    use SoftDeletes;

    protected $table = 'tingkat';

    protected $fillable = [
        'kode_tingkat',
        'nama_tingkat',
    ];

    public function kelas()
    {
        return $this->hasMany(Kelas::class, 'tingkat_id');
    }
}

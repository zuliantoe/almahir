<?php

namespace App\Modules\Akademik\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rombel extends Model
{
    use HasFactory;

    protected $table ='rombel';
    protected $fillable = [
        'tahunajaran_id',
        'kelas_id',
        'siswa_id',
        'no_absen',
        'keterangan'
    ];

    public function tahunAjaran():BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class,'tahunajaran_id');
    }
}

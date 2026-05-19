<?php

namespace Modules\Pendaftaran\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Pendaftaran\Models\Pendaftaran;

class Seleksi extends Model
{
    use HasFactory;

    protected $table = 'seleksis';

    protected $fillable = [
        'pendaftaran_id',
        'nama_tes',
        'tanggal',
        'jam',
        'pengampu',
        'guru_id',
        'metode',
        'lokasi',
        'link',
        'nilai',
    ];

    public function pendaftaran()
    {
        return $this->belongsTo(Pendaftaran::class);
    }

    public function guru()
    {
        return $this->belongsTo(\Modules\Guru\Models\Guru::class, 'guru_id');
    }
}

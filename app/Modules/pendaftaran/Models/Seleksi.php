<?php
namespace Modules\Seleksi\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Pendaftaran\Models\Pendaftaran;

class Seleksi extends Model
{
    use HasFactory;

    protected $table = 'seleksi';

    protected $fillable = [
        'pendaftaran_id',
        'nama_tes',
        'tanggal',
        'jam',
        'pengampu',
        'metode',
        'lokasi',
        'link',
        'nilai',
    ];

    public function pendaftaran()
    {
        return $this->belongsTo(Pendaftaran::class);
    }
}

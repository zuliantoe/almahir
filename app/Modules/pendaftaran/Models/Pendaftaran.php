<?php
namespace Modules\pendaftaran\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Siswa\Models\Siswa;
class Pendaftaran extends Model
{
    protected $table = 'pendaftaran';

    protected $fillable = [
        'nisn',
        'nama_lengkap',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'alamat',
        'no_hp',
        'status',
        'tanggal_daftar',
        'tanggal_diterima',
        'catatan',
    ];

    public function siswa()
    {
        return $this->hasOne(\Modules\Siswa\Models\Siswa::class);
    }
    public function seleksi()
{
    return $this->hasMany(\Modules\Seleksi\Models\Seleksi::class);
}

}


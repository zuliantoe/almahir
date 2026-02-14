<?php
namespace Modules\Pendaftaran\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Siswa\Models\Siswa;
use Modules\Seleksi\Models\Seleksi;
class Pendaftaran extends Model
{

    protected $fillable = [
    'nisn',
    'nama_lengkap',
    'tempat_lahir',
    'tanggal_lahir',
    'jenis_kelamin',
    'berat_badan',
    'tinggi_badan',
    'riwayat_sakit',
    'kelurahan',
    'kecamatan',
    'kota',
    'provinsi',
    'alamat',
    'nama_ayah',
    'pekerjaan_ayah',
    'no_hp',
    'email',
    'status',
    'tanggal_daftar',
    'tanggal_diterima',
    'catatan',
];

    public function siswa()
    {
        return $this->hasOne(Siswa::class);
    }
    public function seleksi()
{
    return $this->hasMany(Seleksi::class);
}


}


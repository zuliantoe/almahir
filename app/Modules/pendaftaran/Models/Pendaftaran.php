<?php

namespace Modules\Pendaftaran\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Siswa\Models\Siswa;
use Modules\Pendaftaran\Models\Seleksi;

class Pendaftaran extends Model
{
    protected $table = 'pendaftarans';
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
        'no_hp_ayah',
        'alamat_ayah',
        'nama_ibu',
        'pekerjaan_ibu',
        'no_hp_ibu',
        'alamat_ibu',
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
    public function seleksis()
    {
        return $this->hasMany(Seleksi::class);
    }
}

<?php

namespace Modules\Keuangan\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Siswa\Models\Siswa;

class PembayaranSiswa extends Model
{
    protected $table = 'pembayaran_siswas';

    protected $fillable = [
        'tagihan_siswa_id',
        'siswa_id',
        'tipe_pembayaran',
        'jumlah_dibayarkan',
        'jumlah_tersisa',
        'tanggal_pembayaran',
        'metode_pembayaran',
        'bukti_gambar',
        'deskripsi',
        'status'
    ];

    protected $casts = [
        'tanggal_pembayaran' => 'date',
        'jumlah_dibayarkan'  => 'decimal:2',
        'jumlah_tersisa'     => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | KONSTANTA STATUS
    |--------------------------------------------------------------------------
    */
    const STATUS_BELUM_LUNAS = 'Belum Lunas';
    const STATUS_LUNAS       = 'Lunas';

    /*
    |--------------------------------------------------------------------------
    | RELASI KE TAGIHAN SISWA
    |--------------------------------------------------------------------------
    */
    public function tagihanSiswa()
    {
        return $this->belongsTo(TagihanSiswa::class, 'tagihan_siswa_id');
    }

    /*
    |--------------------------------------------------------------------------
    | RELASI KE SISWA
    |--------------------------------------------------------------------------
    */
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    /*
    |--------------------------------------------------------------------------
    | CEK SUDAH LUNAS
    |--------------------------------------------------------------------------
    */
    public function getSudahLunasAttribute()
    {
        return $this->status === self::STATUS_LUNAS;
    }

    /*
    |--------------------------------------------------------------------------
    | FORMAT NOMINAL
    |--------------------------------------------------------------------------
    */
    public function getNominalDibayarkanAttribute()
    {
        return 'Rp' . number_format($this->jumlah_dibayarkan, 0, ',', '.');
    }

    public function getNominalTersisaAttribute()
    {
        return 'Rp' . number_format($this->jumlah_tersisa, 0, ',', '.');
    }
}

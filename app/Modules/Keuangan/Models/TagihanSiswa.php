<?php

namespace Modules\Keuangan\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TagihanSiswa extends Model
{
    protected $table = 'tagihan_siswas';

    protected $fillable = [
        'judul',
        'jumlah',
        'tanggal_tagihan',
        'batas_waktu',
        'status',
        'target_id',
        'target_type',
    ];

    protected $casts = [
        'tanggal_tagihan' => 'date',
        'batas_waktu'     => 'date',
        'jumlah'          => 'decimal:2',
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
    | RELASI POLIMORFIK
    |--------------------------------------------------------------------------
    */
    public function target()
    {
        return $this->morphTo();
    }

    /*
    |--------------------------------------------------------------------------
    | RELASI PEMBAYARAN
    |--------------------------------------------------------------------------
    */
    public function pembayaranSiswa()
    {
        return $this->hasMany(PembayaranSiswa::class, 'tagihan_siswa_id');
    }

    /*
    |--------------------------------------------------------------------------
    | TOTAL YANG SUDAH DIBAYARKAN
    |--------------------------------------------------------------------------
    */
    public function getTotalDibayarkanAttribute()
    {
        return $this->pembayaranSiswa()
            ->sum('jumlah_dibayarkan');
    }

    /*
    |--------------------------------------------------------------------------
    | SISA TAGIHAN
    |--------------------------------------------------------------------------
    */
    public function getSisaTagihanAttribute()
    {
        return max($this->jumlah - $this->total_dibayarkan, 0);
    }

    /*
    |--------------------------------------------------------------------------
    | CEK SUDAH LUNAS
    |--------------------------------------------------------------------------
    */
    public function getSudahLunasAttribute()
    {
        return $this->sisa_tagihan <= 0;
    }

    /*
    |--------------------------------------------------------------------------
    | PERBARUI STATUS BERDASARKAN PEMBAYARAN
    |--------------------------------------------------------------------------
    */
    public function perbaruiStatus()
    {
        if ($this->sudah_lunas) {
            $this->status = self::STATUS_LUNAS;
        } else {
            $this->status = self::STATUS_BELUM_LUNAS;
        }

        $this->save();
    }

    /*
    |--------------------------------------------------------------------------
    | CEK TERLAMBAT (REAL-TIME)
    |--------------------------------------------------------------------------
    */
    public function getTerlambatAttribute()
    {
        return $this->status === self::STATUS_BELUM_LUNAS
            && $this->batas_waktu->isPast();
    }

    public function getStatusAktualAttribute()
    {
        if ($this->terlambat) {
            return 'Terlambat';
        }

        return $this->status;
    }

    /*
    |--------------------------------------------------------------------------
    | FORMAT NOMINAL
    |--------------------------------------------------------------------------
    */
    public function getNominalAttribute()
    {
        return 'Rp' . number_format($this->jumlah, 0, ',', '.');
    }

    public function getNominalSisaAttribute()
    {
        return 'Rp' . number_format($this->sisa_tagihan, 0, ',', '.');
    }
}

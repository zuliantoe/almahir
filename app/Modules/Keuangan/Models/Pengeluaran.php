<?php

namespace Modules\Keuangan\Models;

use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    protected $table = 'pengeluarans';

    protected $fillable = [
        'uang_saku_id',
        'tujuan_id',
        'jumlah',
        'tanggal',
        'waktu', 
        'deskripsi'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah' => 'decimal:2'
    ];

    public function tujuan()
    {
        return $this->belongsTo(Tujuan::class);
    }
}
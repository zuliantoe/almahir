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
        'deskripsi',
        'is_otomatis',
        'is_draft'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah' => 'decimal:2',
        'is_draft' => 'boolean'
    ];

    public function tujuan()
    {
        return $this->belongsTo(Tujuan::class);
    }
}
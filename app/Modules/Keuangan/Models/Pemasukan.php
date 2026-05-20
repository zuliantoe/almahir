<?php

namespace Modules\Keuangan\Models;

use Illuminate\Database\Eloquent\Model;

class Pemasukan extends Model
{
    protected $table = 'pemasukans';

    protected $fillable = [
        'uang_saku_id',
        'sumber_id',
        'jumlah',
        'tanggal',
        'waktu', 
        'deskripsi',
        'is_otomatis'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah' => 'decimal:2'
    ];

    public function sumber()
    {
        return $this->belongsTo(Sumber::class);
    }
}
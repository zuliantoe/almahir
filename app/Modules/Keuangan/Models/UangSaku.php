<?php

namespace Modules\Keuangan\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Siswa\Models\Siswa;

class UangSaku extends Model
{
    protected $table = 'uang_sakus';

    protected $fillable = [
        'siswa_id',
        'jumlah',
        'tanggal',
        'status',
        'deskripsi'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah' => 'decimal:2' // Tambahkan cast untuk amount
    ];

    // relasi ke Student
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
    
    // Accessor untuk status default
    public function getStatusAttribute($value)
    {
        return $value ?: 'Belum Diterima Santri';
    }
}
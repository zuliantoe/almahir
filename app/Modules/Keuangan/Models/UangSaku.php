<?php

namespace Modules\Keuangan\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Siswa\Models\Siswa;

class UangSaku extends Model
{
    protected $table = 'uang_sakus';

    protected $fillable = [
        'siswa_id',
        'kelas_id',
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

    // relasi ke Kelas
    public function kelas()
    {
        return $this->belongsTo(\App\Modules\Akademik\Models\Kelas::class, 'kelas_id');
    }
    
    // Accessor untuk status default
    public function getStatusAttribute($value)
    {
        return $value ?: 'Belum Diterima Santri';
    }
}
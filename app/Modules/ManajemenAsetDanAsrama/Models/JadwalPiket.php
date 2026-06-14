<?php

namespace App\Modules\ManajemenAsetDanAsrama\Models;  // SUDAH BENAR

use Illuminate\Database\Eloquent\Model;
use Modules\Siswa\Models\Siswa;  // Gunakan Modules\Siswa...

class JadwalPiket extends Model
{
    protected $table = 'jadwal_piket';

    protected $fillable = [
        'kamar_id',
        'lokasi_piket',
        'tanggal',
        'shift',
        'siswa_id',
        'status',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    // Relasi: jadwal piket milik 1 kamar
    public function kamar()
    {
        return $this->belongsTo(Kamar::class, 'kamar_id');
    }

    // Relasi: jadwal piket milik 1 siswa
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id', 'id')->withTrashed();
    }
}

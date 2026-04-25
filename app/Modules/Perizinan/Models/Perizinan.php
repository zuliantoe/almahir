<?php

namespace Modules\Perizinan\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\PegawaiManager\Models\Pegawai;

class Perizinan extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_izin_pegawai';

    protected $fillable = [
        'user_id', // Ini sebenarnya pegawai_id berdasarkan migrasi lama
        'jenis_izin',
        'tanggal_mulai',
        'tanggal_selesai',
        'alasan',
        'bukti',
        'status',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    /**
     * Relasi ke Pegawai
     */
    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'user_id');
    }
}

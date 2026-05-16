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
        'user_id',
        'jenis_izin',
        'tanggal_mulai',
        'tanggal_selesai',
        'alasan',
        'bukti',
        'status',
        'keterangan_admin',
        'approved_by',
        'potong_gaji',
        'potong_kuota',
        'total_hari',
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

    /**
     * Helper untuk menentukan dampak berdasarkan jenis izin
     */
    public static function getImpactSettings(string $jenis): array
    {
        return match ($jenis) {
            'cuti' => [
                'potong_gaji' => false,
                'potong_kuota' => true,
            ],
            'izin', 'sakit' => [
                'potong_gaji' => true,
                'potong_kuota' => false,
            ],
            default => [
                'potong_gaji' => false,
                'potong_kuota' => false,
            ],
        };
    }
}

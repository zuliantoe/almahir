<?php

namespace Modules\PegawaiManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Model Absensi (PegawaiManager)
 *
 * Mirrors the `absensi` table managed by the Absensi module.
 * Full business logic lives in Modules\Absensi\Models\Absensi.
 *
 * @property string  $id
 * @property string  $pegawai_id
 * @property \Carbon\Carbon $tanggal
 * @property string|null $jam_masuk
 * @property string|null $jam_pulang
 * @property string  $status         HADIR|TERLAMBAT|ALPA|LIBUR|IZIN
 * @property string|null $lat_masuk
 * @property string|null $long_masuk
 * @property string|null $lat_pulang
 * @property string|null $long_pulang
 * @property string|null $keterangan
 */
class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensi';

    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'pegawai_id',
        'tanggal',
        'jam_masuk',
        'jam_pulang',
        'status',
        'lat_masuk',
        'long_masuk',
        'lat_pulang',
        'long_pulang',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function pegawai(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }
}

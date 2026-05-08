<?php

namespace Modules\PegawaiManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Model Izin (PegawaiManager)
 *
 * Points to the actual `pengajuan_izin_pegawai` table.
 * This is a lightweight reference model; the full business logic
 * lives in Modules\Perizinan\Models\Perizinan.
 *
 * @property string  $id
 * @property string  $user_id          FK → pegawai.id
 * @property string  $jenis_izin       izin|sakit|cuti|dinas luar
 * @property \Carbon\Carbon $tanggal_mulai
 * @property \Carbon\Carbon $tanggal_selesai
 * @property string  $alasan
 * @property string|null $bukti
 * @property string  $status           menunggu|disetujui|ditolak
 * @property string|null $approved_by
 * @property string|null $keterangan_admin
 */
class Izin extends Model
{
    use HasFactory;

    /**
     * Actual table used by this module.
     */
    protected $table = 'pengajuan_izin_pegawai';

    /**
     * Primary key is a UUID string (no auto-increment).
     */
    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'user_id',
        'jenis_izin',
        'tanggal_mulai',
        'tanggal_selesai',
        'alasan',
        'bukti',
        'status',
        'approved_by',
        'keterangan_admin',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * The Pegawai who owns this izin request.
     * Note: foreign key `user_id` on this table references `pegawai.id`.
     */
    public function pegawai(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'user_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /** Filter only approved records. */
    public function scopeDisetujui($query)
    {
        return $query->where('status', 'disetujui');
    }

    /** Filter only pending records. */
    public function scopeMenunggu($query)
    {
        return $query->where('status', 'menunggu');
    }
}

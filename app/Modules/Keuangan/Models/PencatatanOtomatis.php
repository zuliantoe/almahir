<?php

namespace Modules\Keuangan\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PencatatanOtomatis extends Model
{
    protected $table = 'pencatatan_otomatis';

    protected $fillable = [
        'tipe',
        'sumber_id',
        'tujuan_id',
        'jumlah',
        'deskripsi',
        'frekuensi',
        'tanggal_mulai',
        'waktu_eksekusi',
        'last_run_at',
        'is_active',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'last_run_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the sumber associated with the pencatatan otomatis.
     */
    public function sumber(): BelongsTo
    {
        return $this->belongsTo(Sumber::class, 'sumber_id');
    }

    /**
     * Get the tujuan associated with the pencatatan otomatis.
     */
    public function tujuan(): BelongsTo
    {
        return $this->belongsTo(Tujuan::class, 'tujuan_id');
    }
}

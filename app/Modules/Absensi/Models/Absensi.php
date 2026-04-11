<?php

namespace Modules\Absensi\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\PegawaiManager\Models\Pegawai;
use Carbon\Carbon;

class Absensi extends Model
{
    use HasUuids;

    protected $table = 'absensi';

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
        'jam_masuk' => 'datetime:H:i:s',
        'jam_pulang' => 'datetime:H:i:s',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS & LOGIC
    |--------------------------------------------------------------------------
    */

    /**
     * Get late duration in minutes
     */
    public function getLateMinutesAttribute(): int
    {
        if ($this->status !== 'TERLAMBAT' || !$this->jam_masuk) {
            return 0;
        }

        $standardTime = Carbon::createFromFormat('H:i:s', '08:00:00');
        $checkInTime = Carbon::parse($this->jam_masuk);

        if ($checkInTime->gt($standardTime)) {
            return $checkInTime->diffInMinutes($standardTime);
        }

        return 0;
    }

    /**
     * Get formatted work duration string
     */
    public function getWorkDurationAttribute(): string
    {
        if (!$this->jam_masuk || !$this->jam_pulang) {
            return '-';
        }

        $start = Carbon::parse($this->jam_masuk);
        $end = Carbon::parse($this->jam_pulang);
        
        $diff = $start->diff($end);
        
        return $diff->format('%h jam %i menit');
    }
}

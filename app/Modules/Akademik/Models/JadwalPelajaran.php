<?php

namespace App\Modules\Akademik\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Guru\Models\Guru;

class JadwalPelajaran extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'jadwal_pelajaran';

    protected $fillable = [
        'hari',
        'jamke',
        'jamawal',
        'jamakhir',
        'mapel_id',
        'rombel_id',
        'guru_id',
    ];

    public function mataPelajaran(): BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class, 'mapel_id');
    }

    public function rombel(): BelongsTo
    {
        return $this->belongsTo(Rombel::class, 'rombel_id');
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }
}

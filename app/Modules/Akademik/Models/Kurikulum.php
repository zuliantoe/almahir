<?php

namespace App\Modules\Akademik\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kurikulum extends Model
{
    use HasFactory;

    protected $table = 'kurikulum';
    protected $fillable = [
        'tahunajaran_id',
        'kelas_id',
        'matpel_id',
        'total_jam',
        'semester',
        'deskripsi'
    ];

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class,'tahunajaran_id');
    }

    public function kelas():BelongsTo
    {
        return $this->belongsTo(kelas::class,'kelas_id');
    }

    public function mataPelajaran():BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class,'matapelajaran_id');
    }
}

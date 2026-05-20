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
        'master_kurikulum_id',
        'tingkat_id',
        'tahunajaran_id',
        'kelas_id',
        'mapel_id',
        'totaljam',
        'kkm',
    ];

    protected $attributes = [
        'totaljam' => 0,
    ];

    public function masterKurikulum(): BelongsTo
    {
        return $this->belongsTo(MasterKurikulum::class, 'master_kurikulum_id');
    }

    public function tingkat(): BelongsTo
    {
        return $this->belongsTo(Tingkat::class, 'tingkat_id');
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class,'tahunajaran_id');
    }

    public function kelas():BelongsTo
    {
        return $this->belongsTo(Kelas::class,'kelas_id');
    }

    public function mataPelajaran():BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class,'mapel_id');
    }
}

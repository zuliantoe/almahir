<?php

namespace App\Modules\Akademik\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Siswa\Models\Siswa;

class RombelSiswa extends Model
{
    use HasFactory;

    protected $table = 'rombel_siswa';
    
    protected $fillable = [
        'rombel_id', 
        'siswa_id',
        'tahunajaran_id',
        'kelas_id',
        'status'
    ];
    
    public function rombel(): BelongsTo 
    { 
        return $this->belongsTo(Rombel::class, 'rombel_id'); 
    }
    
    public function siswa(): BelongsTo 
    { 
        return $this->belongsTo(Siswa::class, 'siswa_id'); 
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class, 'tahunajaran_id');
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }
}

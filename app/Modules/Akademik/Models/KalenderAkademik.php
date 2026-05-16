<?php

namespace App\Modules\Akademik\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KalenderAkademik extends Model
{
    use HasFactory;

    protected $table = 'kalender_akademik';
    protected $fillable = ['tahunajaran_id','semester','kegiatan_id','nama_kegiatan','tanggal_awal','tanggal_akhir','deskripsi','status'
    ];
    protected $casts = [
        'tanggal_awal'=> 'date',
        'tanggal_akhir'=> 'date',
    ];
    public function tahunAjaran():BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class,'tahunajaran_id');
    }
    public function jenisKegiatan():BelongsTo
    {
        return $this->belongsTo(JenisKegiatan::class,'kegiatan_id');
    }
}

<?php

namespace Modules\PenilaianDanPresensi\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Siswa\Models\Siswa;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Models\User;

class RaportCatatan extends Model
{
    protected $table = 'penilaian_raport_catatan';

    protected $fillable = [
        'siswa_id',
        'tahunajaran_id',
        'catatan',
        'catatan_tahfidz',
        'semester',
        'author_id',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahunajaran_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}

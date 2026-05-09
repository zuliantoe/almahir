<?php

namespace App\Modules\ManajemenAsetDanAsrama\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class Kerusakan extends Model
{
    use SoftDeletes;
    protected $table = 'kerusakan';

    protected $fillable = [
        'aset_id',
        'tanggal_rusak',
        'tanggal_kerusakan',
        'deskripsi_kerusakan',
        'tingkat_kerusakan',
        'status_penanganan',
        'pelapor',
        'catatan',
        'deleted_by',
        'alasan_hapus',
    ];

    protected $casts = [
        'tanggal_rusak' => 'date',
        'tanggal_kerusakan' => 'date',
    ];

    // Relasi: kerusakan milik 1 aset
    public function aset()
    {
        return $this->belongsTo(Aset::class, 'aset_id');
    }

    // Relasi: user yang menghapus
    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}

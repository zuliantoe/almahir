<?php

namespace App\Modules\ManajemenAsetDanAsrama\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class Pemeliharaan extends Model
{
    use SoftDeletes;
    protected $table = 'pemeliharaan';

    protected $fillable = [
        'aset_id',
        'tanggal_mulai_pemeliharaan',
        'tanggal_selesai_pemeliharaan',
        'tanggal_pemeliharaan',
        'deskripsi_pemeliharaan',
        'biaya_pemeliharaan',
        'biaya',
        'catatan',
        'status',
        'catatan_selesai',
        'deleted_by',
        'alasan_hapus',
    ];

    protected $casts = [
        'tanggal_mulai_pemeliharaan' => 'date',
        'tanggal_selesai_pemeliharaan' => 'date',
        'tanggal_pemeliharaan' => 'date',
        'biaya_pemeliharaan' => 'decimal:2',
        'biaya' => 'decimal:2',
    ];

    // Relasi: pemeliharaan milik 1 aset
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

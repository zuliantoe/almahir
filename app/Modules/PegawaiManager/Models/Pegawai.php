<?php

namespace Modules\PegawaiManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pegawai extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * Table name
     */
    protected $table = 'pegawai';

    /**
     * UUID settings
     */
    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * Fillable fields (sesuai migration lu)
     */
    protected $fillable = [
        'user_id',
        'nama',
        'type_pegawai_id',
        'no_hp',
        'email',
        'alamat',
        'tanggal_masuk',
        'foto'
    ];

    /**
     * Casts
     */
    protected $casts = [
        'tanggal_masuk' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}

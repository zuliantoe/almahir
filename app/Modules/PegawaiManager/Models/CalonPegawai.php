<?php

namespace Modules\PegawaiManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CalonPegawai extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'calon_pegawai';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'type_pegawai_id',
        'nama',
        'email',
        'no_hp',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'alamat',
        'berkas_cv',
        'berkas_lamaran',
        'status_seleksi',
        'tanggal_melamar',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_melamar' => 'date',
    ];

    /**
     * Relasi ke Master Data Tipe Pegawai (Jabatan yang dilamar)
     */
    public function typePegawai(): BelongsTo
    {
        return $this->belongsTo(TypePegawai::class, 'type_pegawai_id');
    }
}

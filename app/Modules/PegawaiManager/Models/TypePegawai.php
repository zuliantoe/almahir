<?php

namespace Modules\PegawaiManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class TypePegawai extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'type_pegawai';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nama_type',
    ];
    public function pegawai()
    {
        return $this->hasMany(Pegawai::class, 'type_pegawai_id');
    }
}


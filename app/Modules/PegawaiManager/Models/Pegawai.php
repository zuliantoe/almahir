<?php

namespace Modules\PegawaiManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;

class Pegawai extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'pegawai';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nama',
        'user_id',
        'type_pegawai_id',
        'no_hp',
        'email',
        'alamat',
        'tanggal_masuk',
        'foto',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    // 🔥 Pegawai belongs to User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // 🔥 Pegawai belongs to TypePegawai
    public function typePegawai(): BelongsTo
    {
        return $this->belongsTo(TypePegawai::class, 'type_pegawai_id');
    }

    public function izins(): HasMany
    {
        return $this->hasMany(Izin::class);
    }

    public function absensis(): HasMany
    {
        return $this->hasMany(Absensi::class);
    }
}

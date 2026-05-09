<?php

namespace Modules\WaliMurid\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Siswa\Models\Siswa;

/**
 * WaliMurid Model
 *
 * Represents a parent/guardian in the academic system.
 */
class WaliMurid extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'wali_murid';

    protected $fillable = [
        'nama',
        'email',
        'telepon',
        'alamat',
        'pekerjaan',
        'hubungan',
    ];

    /**
     * Get the user account associated with this wali murid.
     */
    public function user()
    {
        return $this->morphOne(User::class, 'ref');
    }

    /**
     * Get the students this wali murid is responsible for.
     */
    public function siswa()
    {
        return $this->belongsToMany(Siswa::class, 'siswa_wali', 'wali_murid_id', 'siswa_id')
            ->withTimestamps();
    }
}

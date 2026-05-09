<?php

namespace Modules\Siswa\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Siswa Model
 * 
 * Represents a student in the academic system.
 * Uses UUID as primary key for better distribution and security.
 * 
 * @property string $id UUID primary key
 * @property string $nis Nomor Induk Siswa (Student ID Number)
 * @property string $nama Student's full name
 * @property string $email Student's email
 * @property string $tanggal_lahir Date of birth
 * @property string $tempat_lahir Place of birth
 * @property string $jenis_kelamin Gender (L/P)
 * @property string $alamat Address
 * @property string $telepon Phone number
 * @property string $kelas_id Current class ID
 * 
 * @author SIAKAD Development Team
 */
class Siswa extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected $table = 'siswa';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'nis',
        'nama',
        'email',
        'tanggal_lahir',
        'tempat_lahir',
        'jenis_kelamin',
        'alamat',
        'telepon',
        'foto',
        'kelas_id',
        'tahun_masuk',
        'status', // aktif, lulus, keluar, cuti
        'pendaftaran_id',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'tanggal_lahir' => 'date',
        'tahun_masuk' => 'integer',
    ];

    /**
     * Get the user account associated with this student.
     * 
     * This is the inverse of the polymorphic relationship.
     * A Siswa can have one User account linked via ref_id.
     */
    public function user()
    {
        return $this->morphOne(\App\Models\User::class, 'ref');
    }

    /**
     * Get the class this student belongs to.
     */
    public function kelas()
    {
        return $this->belongsTo(\App\Modules\Akademik\Models\Kelas::class, 'kelas_id');
    }

    /**
     * Scope: Filter active students only.
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    /**
     * Get formatted NIS with prefix.
     */
    public function getNisFormattedAttribute(): string
    {
        return 'NIS-' . $this->nis;
    }
    public function pendaftaran()
    {
        return $this->belongsTo(\Modules\Pendaftaran\Models\Pendaftaran::class);
    }

    public function rombel()
    {
        return $this->belongsToMany(\App\Modules\Akademik\Models\Rombel::class, 'rombel_siswa', 'siswa_id', 'rombel_id')
            ->withPivot('status')
            ->withTimestamps();
    }

    public function currentRombel()
    {
        return $this->rombel()->wherePivot('status', 'aktif')->latest()->first();
    }

    /**
     * Relationship to KamarPenghuni (Dormitory)
     */
    public function kamarPenghuni()
    {
        return $this->hasMany(\App\Modules\ManajemenAsetDanAsrama\Models\KamarPenghuni::class, 'siswa_id');
    }

    public function rombelSiswa()
    {
        return $this->hasMany(\App\Modules\Akademik\Models\RombelSiswa::class, 'siswa_id');
    }

    public function wali()
    {
        return $this->belongsToMany(\Modules\WaliMurid\Models\WaliMurid::class, 'siswa_wali', 'siswa_id', 'wali_murid_id')
            ->withTimestamps();
    }
}

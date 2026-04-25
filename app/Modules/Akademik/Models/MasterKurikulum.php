<?php

namespace App\Modules\Akademik\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterKurikulum extends Model
{
    use SoftDeletes;

    protected $table = 'master_kurikulum';

    protected $fillable = [
        'nama_kurikulum',
        'status',
    ];

    public function detailKurikulum()
    {
        return $this->hasMany(Kurikulum::class, 'master_kurikulum_id');
    }
}

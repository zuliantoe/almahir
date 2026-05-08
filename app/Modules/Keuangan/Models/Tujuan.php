<?php

namespace Modules\Keuangan\Models;

use Illuminate\Database\Eloquent\Model;

class Tujuan extends Model
{
    protected $table = 'tujuans';

    protected $fillable = ['nama'];

    public function pengeluarans()
    {
        return $this->hasMany(Pengeluaran::class);
    }
}
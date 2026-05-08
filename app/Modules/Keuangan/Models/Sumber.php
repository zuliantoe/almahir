<?php

namespace Modules\Keuangan\Models;

use Illuminate\Database\Eloquent\Model;

class Sumber extends Model
{
    protected $table = 'sumbers';

    protected $fillable = ['nama'];

    public function pemasukans()
    {
        return $this->hasMany(Pemasukan::class);
    }
}
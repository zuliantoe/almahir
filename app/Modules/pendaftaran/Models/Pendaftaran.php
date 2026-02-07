<?php
namespace Modules\pendaftaran\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Siswa\Models\Siswa;
class Pendaftaran extends Model
{
    protected $table = 'pendaftaran';

    public function siswa()
    {
        return $this->hasOne(Siswa::class);
    }
}

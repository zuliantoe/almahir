<?php

namespace Modules\Pendaftaran\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateSeleksiItem extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function template()
    {
        return $this->belongsTo(TemplateSeleksi::class, 'template_seleksi_id');
    }
}

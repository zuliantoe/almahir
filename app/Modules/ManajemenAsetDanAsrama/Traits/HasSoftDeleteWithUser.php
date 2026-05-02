<?php
namespace Modules\ManajemenAsetDanAsrama\Traits;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

trait HasSoftDeleteWithUser
{
    /**
     * Perform soft delete with user and reason tracking.
     * 
     * @param Request $request
     * @param Model $model
     * @return void
     */
    protected function performSoftDelete(Request $request, Model $model): void
    {
        $request->validate([
            'alasan_hapus' => 'required|string'
        ]);

        $model->deleted_by = auth()->id();
        $model->alasan_hapus = $request->alasan_hapus;
        $model->save();
        $model->delete();
    }
}

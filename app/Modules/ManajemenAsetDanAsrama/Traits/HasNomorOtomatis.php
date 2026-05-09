<?php
namespace App\Modules\ManajemenAsetDanAsrama\Traits;

use Illuminate\Support\Facades\DB;

trait HasNomorOtomatis
{
    /**
     * Generate nomor otomatis (e.g., PJ-202404-0001)
     */
    public function generateNomor(string $modelClass, string $prefix, string $dateColumn = 'created_at'): string
    {
        $yearMonth = date('Ym');
        $year = date('Y');
        $month = date('m');

        // Tentukan field berdasarkan prefix
        $field = ($prefix === 'PJ') ? 'nomor_pengajuan' : 'nomor_po';

        // Cek apakah model punya trait SoftDeletes
        $hasSoftDeletes = in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($modelClass));
        
        $baseQuery = $hasSoftDeletes ? $modelClass::withTrashed() : $modelClass::query();

        // Hitung semua data di bulan ini
        $count = (clone $baseQuery)
                    ->whereYear($dateColumn, $year)
                    ->whereMonth($dateColumn, $month)
                    ->count();

        $nomorUrut = $count + 1;

        // Safety loop: Pastikan nomor bener-bener belum ada di DB
        do {
            $nomor = "{$prefix}-{$yearMonth}-" . str_pad($nomorUrut, 4, '0', STR_PAD_LEFT);
            $exists = (clone $baseQuery)->where($field, $nomor)->exists();
            
            if ($exists) {
                $nomorUrut++;
            }
        } while ($exists);

        return $nomor;
    }
}

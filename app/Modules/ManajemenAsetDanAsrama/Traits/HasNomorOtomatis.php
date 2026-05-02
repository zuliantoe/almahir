<?php
namespace Modules\ManajemenAsetDanAsrama\Traits;

use Illuminate\Support\Facades\DB;

trait HasNomorOtomatis
{
    /**
     * Generate nomor otomatis (e.g., PJ-202404-0001)
     * 
     * @param string $modelClass Class name of the model
     * @param string $prefix Prefix (PJ, PO, etc.)
     * @param string $dateColumn Column to check for date (default: created_at)
     * @return string
     */
    public function generateNomor(string $modelClass, string $prefix, string $dateColumn = 'created_at'): string
    {
        $yearMonth = date('Ym');
        $year = date('Y');
        $month = date('m');

        $count = $modelClass::whereYear($dateColumn, $year)
                    ->whereMonth($dateColumn, $month)
                    ->count();

        $nomorUrut = str_pad($count + 1, 4, '0', STR_PAD_LEFT);

        return "{$prefix}-{$yearMonth}-{$nomorUrut}";
    }
}

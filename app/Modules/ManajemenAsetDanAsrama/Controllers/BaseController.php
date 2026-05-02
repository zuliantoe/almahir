<?php

namespace App\Modules\ManajemenAsetDanAsrama\Controllers;

use App\Http\Controllers\Controller;

abstract class BaseController extends Controller
{
    /**
     * Get Indonesian day name from English day.
     */
    protected function getHariIndo(string $day): string
    {
        $hari = [
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu',
            'Sunday'    => 'Minggu'
        ];
        
        return $hari[$day] ?? $day;
    }
}
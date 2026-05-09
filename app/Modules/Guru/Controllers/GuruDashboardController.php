<?php

namespace Modules\Guru\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class GuruDashboardController extends Controller
{
    public function index()
    {
        return redirect()->route('penilaiandanpresensi.index');
    }
}

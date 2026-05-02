<?php

namespace Modules\Siswa\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class SiswaDashboardController extends Controller
{
    public function index(): View
    {
        return view('siswa::dashboard', [
            'title' => 'Dashboard Santri',
            'breadcrumb' => 'Dashboard'
        ]);
    }
}

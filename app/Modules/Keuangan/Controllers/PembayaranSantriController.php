<?php

namespace Modules\Keuangan\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PembayaranSantriController extends Controller
{
    public function index()
    {
        return view('keuangan::pembayaransantris.index');
    }
}

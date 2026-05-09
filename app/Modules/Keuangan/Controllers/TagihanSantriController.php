<?php

namespace Modules\Keuangan\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TagihanSantriController extends Controller
{
    public function index()
    {
        return view('keuangan::tagihansantris.index');
    }
}

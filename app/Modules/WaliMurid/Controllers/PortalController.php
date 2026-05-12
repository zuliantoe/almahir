<?php

namespace Modules\WaliMurid\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PortalController extends Controller
{
    /**
     * Display Wali Murid Dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Ensure user is linked to a WaliMurid record
        if (!$user->ref_id || $user->ref_type !== \Modules\WaliMurid\Models\WaliMurid::class) {
            return redirect('/dashboard')->with('error', 'Akun Anda tidak terhubung dengan data Wali Murid.');
        }

        $wali = $user->ref;
        $siswas = $wali->siswa()->with(['kelas'])->get();

        return view('walimurid::portal.dashboard', [
            'title' => 'Portal Wali Murid',
            'wali' => $wali,
            'siswas' => $siswas,
        ]);
    }

    /**
     * Display specific student detail for parent
     */
    public function siswaDetail($id)
    {
        $user = Auth::user();
        $wali = $user->ref;

        // Ensure this wali is actually linked to this student
        $siswa = $wali->siswa()->findOrFail($id);

        return view('walimurid::portal.siswa_detail', [
            'title' => 'Detail Siswa: ' . $siswa->nama,
            'siswa' => $siswa,
        ]);
    }
}

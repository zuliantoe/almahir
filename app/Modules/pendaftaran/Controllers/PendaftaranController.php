<?php

namespace Modules\Pendaftaran\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Pendaftaran\Models\Pendaftaran;
use Modules\Siswa\Models\Siswa;

class PendaftaranController extends Controller
{
   
    public function index()
    {
        $pendaftaran = Pendaftaran::latest()->get();
        return view('pendaftaran::index', compact('pendaftaran'));
    }

    public function create()
    {
        return view('pendaftaran::create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nisn' => 'required|unique:pendaftaran,nisn',
            'nama_lengkap' => 'required',
            'tanggal_lahir' => 'required|date',
            'tempat_lahir' => 'required',
            'jenis_kelamin' => 'required',
            'alamat' => 'required',
            'no_hp' => 'required',
        ]);

        Pendaftaran::create([
            'nisn' => $request->nisn,
            'nama_lengkap' => $request->nama_lengkap,
            'tanggal_lahir' => $request->tanggal_lahir,
            'tempat_lahir' => $request->tempat_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'alamat' => $request->alamat,
            'no_hp' => $request->no_hp,
            'status' => 'pending',
            'tanggal_daftar' => now(),
        ]);

        return redirect()->back()->with('success', 'Pendaftaran berhasil');
    }

}

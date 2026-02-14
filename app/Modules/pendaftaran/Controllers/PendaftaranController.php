<?php

namespace Modules\Pendaftaran\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Pendaftaran\Models\Pendaftaran;

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
        $validated = $request->validate([
            'nisn' => 'required|string|max:20|unique:pendaftarans,nisn',
            'nama_lengkap' => 'required|string',
            'tempat_lahir' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',

            'berat_badan' => 'required|numeric|min:20',
            'tinggi_badan' => 'required|numeric',
            'riwayat_sakit' => 'nullable|string',

            'kelurahan' => 'required|string',
            'kecamatan' => 'required|string',
            'kota' => 'required|string',
            'provinsi' => 'required|string',
            'alamat' => 'required|string',

            'nama_ayah' => 'required|string',
            'pekerjaan_ayah' => 'required|string',
            'no_hp' => 'required|string|max:15',
            'email' => 'required|email|unique:pendaftarans,email',
        ]);

        /*
        |--------------------------------------------------------------------------
        | LOGIKA SELEKSI OTOMATIS
        |--------------------------------------------------------------------------
        */
        $status = 'pending';
        $catatan = null;

        if ($validated['tinggi_badan'] < 150) {
            $status = 'ditolak';
            $catatan = 'Tinggi badan kurang dari standar minimal (150 cm)';
        }

        if ($validated['berat_badan'] < 35) {
            $status = 'ditolak';
            $catatan = 'Berat badan kurang dari standar minimal (35 kg)';
        }

        if (!empty($validated['riwayat_sakit'])) {
            $status = 'diproses';
            $catatan = 'Perlu pemeriksaan riwayat sakit';
        }

        /*
        |--------------------------------------------------------------------------
        | SIMPAN DATA
        |--------------------------------------------------------------------------
        */
        $validated['status'] = $status;
        $validated['catatan'] = $catatan;
        $validated['tanggal_daftar'] = now();

        Pendaftaran::create($validated);

        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */
        if ($status === 'ditolak') {
            return redirect()
                ->route('pendaftaran.create')
                ->with('warning', $catatan);
        }

        return redirect()
            ->route('pendaftaran.create')
            ->with('success', 'Pendaftaran berhasil dikirim');
    }
}

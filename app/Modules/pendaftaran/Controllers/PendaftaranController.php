<?php

namespace Modules\Pendaftaran\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Pendaftaran\Models\Pendaftaran;

class PendaftaranController extends Controller
{
    public function index(Request $request)
    {
        $query = Pendaftaran::query();

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $data = $query->latest()->get();

        $totalPendaftar = Pendaftaran::count();
        $totalDiterima = Pendaftaran::where('status', 'diterima')->count();
        $totalDitolak = Pendaftaran::where('status', 'ditolak')->count();

        return view('pendaftaran::admin.pendaftaran', compact('data', 'totalPendaftar', 'totalDiterima', 'totalDitolak'));
    }


    public function show($id)
    {
        $pendaftaran = Pendaftaran::with('seleksis')->findOrFail($id);

        return view('pendaftaran::admin.show', compact('pendaftaran'));
    }
    public function create()
    {
        return view('pendaftaran::create');
    }

    public function store(Request $request)
    {
        $currentYear = date('Y');
        $minYear = $currentYear - 18; // minimal umur 3 tahun
        $maxYear = $currentYear - 3;  // maksimal umur 18 tahun

        $validated = $request->validate([

            'nisn' => 'required|digits_between:5,20|unique:pendaftarans,nisn',

            'nama_lengkap' => 'required|string|max:255',

            'tempat_lahir' => 'required|string|max:255',

            'tanggal_lahir' => [
                'required',
                'date',
                "after_or_equal:$minYear-01-01",
                "before_or_equal:$maxYear-12-31",
            ],

            'jenis_kelamin' => 'required|in:L,P',

            'berat_badan' => 'required|numeric|min:20',

            'tinggi_badan' => 'required|numeric|min:50',

            'riwayat_sakit' => 'required|string',

            'kelurahan' => 'required|string',
            'kecamatan' => 'required|string',
            'kota' => 'required|string',
            'provinsi' => 'required|string',
            'alamat' => 'required|string',

            'nama_ayah' => 'required|string',
            'pekerjaan_ayah' => 'required|string',

            'no_hp' => 'required|digits_between:10,15',

            'email' => 'required|email|unique:pendaftarans,email',

        ]);

        $validated['tanggal_daftar'] = now();
        $validated['status'] = 'pending';

        Pendaftaran::create($validated);

        return redirect('/pendaftaran')
            ->with('success', 'Pendaftaran berhasil');
    }
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,diproses,diterima,ditolak',
        ]);

        $pendaftaran = Pendaftaran::findOrFail($id);

        if (in_array($request->status, ['diterima', 'ditolak'])) {
            $hasUnscoredTests = \Modules\Pendaftaran\Models\Seleksi::where('pendaftaran_id', $id)
                                ->whereNull('nilai')
                                ->exists();
                                
            if ($hasUnscoredTests) {
                return back()->with('error', 'Terdeteksi ada tes yang belum selesai, silahkan selesaikan proses tes.');
            }
        }

        $updateData = ['status' => $request->status];
        if ($request->status === 'diterima') {
            $updateData['tanggal_diterima'] = now();
        }

        $pendaftaran->update($updateData);

        return back()->with('success', 'Status pendaftaran berhasil diperbarui');
    }

    public function updateCatatan(Request $request, $id)
    {
        $request->validate([
            'catatan' => 'nullable|string'
        ]);

        $pendaftaran = Pendaftaran::findOrFail($id);
        $pendaftaran->update([
            'catatan' => $request->catatan,
        ]);

        return back()->with('success', 'Catatan berhasil diperbarui');
    }
}

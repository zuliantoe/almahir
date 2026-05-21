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

        // Default ke 'pending' jika status kosong atau tidak ada
        $currentStatus = $request->status ?: 'pending';

        if ($currentStatus !== 'all') {
            $query->where('status', $currentStatus);
        }

        $data = $query->orderBy('tanggal_daftar', 'desc')->get()->groupBy(function($item) {
            $date = \Carbon\Carbon::parse($item->tanggal_daftar)->startOfDay();
            $today = \Carbon\Carbon::today();

            if ($date->equalTo($today)) {
                return 'Hari Ini';
            } elseif ($date->equalTo($today->copy()->subDay())) {
                return 'Kemarin';
            } elseif ($date->greaterThanOrEqualTo($today->copy()->startOfWeek())) {
                return 'Minggu Ini';
            } elseif ($date->greaterThanOrEqualTo($today->copy()->startOfMonth())) {
                return 'Bulan Ini';
            } else {
                // Hitung selisih bulan absolut
                $monthDiff = ($today->year - $date->year) * 12 + ($today->month - $date->month);
                if ($monthDiff == 1) {
                    return '1 Bulan Lalu';
                }
                return $monthDiff . ' Bulan Lalu';
            }
        });

        return view('pendaftaran::admin.pendaftaran', compact('data', 'currentStatus'));
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
        $minYear = $currentYear - 17; // maksimal umur 17 tahun
        $maxYear = $currentYear - 13; // minimal umur 13 tahun

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
            'no_hp_ayah' => 'required|string|max:20',
            'alamat_ayah' => 'required|string',

            'nama_ibu' => 'required|string',
            'pekerjaan_ibu' => 'required|string',
            'no_hp_ibu' => 'required|string|max:20',
            'alamat_ibu' => 'required|string',

            'email' => 'required|email|unique:pendaftarans,email',

        ], [
            'tanggal_lahir.after_or_equal' => 'Usia calon siswa maksimal harus 17 tahun.',
            'tanggal_lahir.before_or_equal' => 'Usia calon siswa minimal harus 13 tahun.',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
            'tanggal_lahir.date' => 'Format tanggal lahir tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar di sistem.',
            'nisn.unique' => 'NISN ini sudah terdaftar di sistem.',
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
            $totalTests = \Modules\Pendaftaran\Models\Seleksi::where('pendaftaran_id', $id)->count();

            if ($totalTests === 0) {
                return back()->with('error', 'Belum ada jadwal tes untuk pendaftaran ini. Silahkan buat jadwal tes terlebih dahulu.');
            }

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

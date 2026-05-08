<?php

namespace App\Modules\ManajemenAsetDanAsrama\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Modules\ManajemenAsetDanAsrama\Models\JadwalPiket;
use App\Modules\ManajemenAsetDanAsrama\Models\Kamar;
use Modules\Siswa\Models\Siswa;

class JadwalPiketController extends BaseController
{
    /**
     * Display a listing of jadwal piket.
     */
    public function index(Request $request): View
    {
        $query = JadwalPiket::with(['siswa', 'kamar']);

        // Filter Nama Santri (Search)
        if ($request->filled('q')) {
            $query->whereHas('siswa', function($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->q . '%');
            });
        }

        // Filter Lokasi Piket
        if ($request->filled('lokasi_piket')) {
            $query->where('lokasi_piket', $request->lokasi_piket);
        }

        if ($request->filled('kamar_id')) {
            $query->where('kamar_id', $request->kamar_id);
        }
        if ($request->filled('tanggal_mulai')) {
            $query->where('tanggal', '>=', $request->tanggal_mulai);
        }
        if ($request->filled('tanggal_selesai')) {
            $query->where('tanggal', '<=', $request->tanggal_selesai);
        }

        $totalSantri = Siswa::count() ?: 15; // Fallback ke 15 jika kosong

        $jadwal = $query->orderBy('tanggal', 'desc')
                    ->orderBy('shift', 'asc')
                    ->orderBy('lokasi_piket', 'asc')
                    ->paginate($totalSantri)
                    ->withQueryString();
        
        $kamar = Kamar::all();
        $locations = JadwalPiket::whereNotNull('lokasi_piket')->distinct()->pluck('lokasi_piket');

        return view('manajemenasetdanasrama::jadwal-piket.index', [
            'title'        => 'Jadwal Piket Asrama',
            'jadwal'       => $jadwal,
            'kamar'        => $kamar,
            'locations'    => $locations,
            'totalSantri'  => $totalSantri,
        ]);
    }

    /**
     * Show the form for creating a new jadwal piket.
     */
    public function create(): View
    {
        $siswa = Siswa::all();
        $kamar = Kamar::all();
        
        return view('manajemenasetdanasrama::jadwal-piket.create', [
            'title' => 'Tambah Jadwal Piket',
            'siswa' => $siswa,
            'kamar' => $kamar,
        ]);
    }

    /**
     * Store a newly created jadwal piket in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->getValidationRules($request));
        $validated['status'] = 'belum';

        JadwalPiket::create($validated);

        return redirect()->route('manajemenasetdanasrama.jadwal-piket.index')
            ->with('success', 'Jadwal piket berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified jadwal piket.
     */
    public function edit(string $id): View
    {
        $jadwal = JadwalPiket::findOrFail($id);
        $siswa = Siswa::all();
        $kamar = Kamar::all();
        
        return view('manajemenasetdanasrama::jadwal-piket.edit', [
            'title'  => 'Edit Jadwal Piket',
            'jadwal' => $jadwal,
            'siswa'  => $siswa,
            'kamar'  => $kamar,
        ]);
    }

    /**
     * Update the specified jadwal piket in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $jadwal = JadwalPiket::findOrFail($id);
        $validated = $request->validate($this->getValidationRules($request, $id));

        $jadwal->update($validated);

        return redirect()->route('manajemenasetdanasrama.jadwal-piket.index')
            ->with('success', 'Jadwal piket berhasil diperbarui.');
    }

    /**
     * Get common validation rules.
     */
    private function getValidationRules(Request $request, ?string $id = null): array
    {
        return [
            'kamar_id' => 'required|exists:kamar,id',
            'tanggal'  => 'required|date',
            'siswa_id' => [
                'required',
                'exists:siswa,id',
                \Illuminate\Validation\Rule::unique('jadwal_piket')->where(function ($query) use ($request) {
                    return $query->where('kamar_id', $request->kamar_id)
                                 ->where('tanggal', $request->tanggal);
                })->ignore($id),
            ],
        ];
    }

    /**
     * Remove the specified jadwal piket from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        $jadwal = JadwalPiket::findOrFail($id);
        $jadwal->delete();

        return redirect()->route('manajemenasetdanasrama.jadwal-piket.index')
            ->with('success', 'Jadwal piket berhasil dihapus.');
    }

    /**
     * Mark jadwal piket as completed.
     */
    public function selesai(string $id): RedirectResponse
    {
        $jadwal = JadwalPiket::findOrFail($id);
        $jadwal->status = 'sudah';
        $jadwal->save();

        return redirect()->back()
            ->with('success', 'Status piket diupdate menjadi selesai.');
    }

    /**
     * Auto generate jadwal piket (Round Robin)
     */
    public function autoGenerate(Request $request)
    {
        $request->validate([
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'shift'           => 'required|in:pagi,sore,malam',
            'lokasi'          => 'required|array',
            'jumlah_santri'   => 'required|array',
        ]);

        try {
            // Mapping input lokasi & kuota menjadi format array yang dipahami Service
            $locations = [];
            foreach ($request->lokasi as $index => $nama) {
                $locations[] = [
                    'nama' => $nama,
                    'kuota' => $request->jumlah_santri[$index] ?? 0
                ];
            }

            $service = new \App\Modules\ManajemenAsetDanAsrama\Services\JadwalPiketService();
            $generated = $service->generateSmart(
                $request->tanggal_mulai,
                $request->tanggal_selesai,
                $request->shift,
                $locations
            );

            if ($generated === 0) {
                return redirect()->back()->with('warning', 'Tidak ada jadwal yang di-generate. Mungkin rentang tanggal terlalu pendek atau santri sudah penuh di shift tersebut.');
            }

            return redirect()->back()->with('success', "Berhasil me-generate {$generated} jadwal piket secara cerdas & adil.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Print printer-friendly version of the schedule.
     */
    public function print(Request $request)
    {
        $query = JadwalPiket::with(['kamar', 'siswa']);

        if ($request->filled('kamar_id')) {
            $query->where('kamar_id', $request->kamar_id);
        }

        if ($request->filled('tanggal_mulai')) {
            $query->where('tanggal', '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_selesai')) {
            $query->where('tanggal', '<=', $request->tanggal_selesai);
        }

        $jadwal = $query->orderBy('tanggal', 'asc')->get();
        $kamar = Kamar::find($request->kamar_id);

        return view('manajemenasetdanasrama::jadwal-piket.print', [
            'title'  => 'Cetak Jadwal Piket',
            'jadwal' => $jadwal,
            'kamar'  => $kamar,
            'request'=> $request
        ]);
    }

    /**
     * Remove all jadwal piket.
     */
    public function resetAll(): RedirectResponse
    {
        JadwalPiket::truncate();

        return redirect()->route('manajemenasetdanasrama.jadwal-piket.index')
            ->with('success', 'Semua jadwal piket berhasil di-reset.');
    }
}
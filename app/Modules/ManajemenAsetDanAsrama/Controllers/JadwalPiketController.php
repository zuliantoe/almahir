<?php

namespace App\Modules\ManajemenAsetDanAsrama\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Modules\ManajemenAsetDanAsrama\Models\JadwalPiket;
use Modules\Siswa\Models\Siswa;

class JadwalPiketController extends BaseController
{
    /**
     * Display a listing of jadwal piket.
     */
    public function index(Request $request): View
    {
        $query = JadwalPiket::with(['siswa', 'kamar']);

        if ($request->filled('kamar_id')) {
            $query->where('kamar_id', $request->kamar_id);
        }
        if ($request->filled('tanggal_mulai')) {
            $query->where('tanggal', '>=', $request->tanggal_mulai);
        }
        if ($request->filled('tanggal_selesai')) {
            $query->where('tanggal', '<=', $request->tanggal_selesai);
        }

        $jadwal = $query->orderBy('tanggal', 'desc')
                    ->orderBy('kamar_id')
                    ->paginate(15)
                    ->withQueryString();
        
        $kamar = \App\Modules\ManajemenAsetDanAsrama\Models\Kamar::all();

        return view('manajemenasetdanasrama::jadwal-piket.index', [
            'title'  => 'Jadwal Piket Asrama',
            'jadwal' => $jadwal,
            'kamar'  => $kamar,
        ]);
    }

    /**
     * Show the form for creating a new jadwal piket.
     */
    public function create(): View
    {
        $siswa = Siswa::all();
        $kamar = \App\Modules\ManajemenAsetDanAsrama\Models\Kamar::all();
        
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
        $kamar = \App\Modules\ManajemenAsetDanAsrama\Models\Kamar::all();
        
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
            'kamar_id'       => 'required|exists:kamar,id',
            'tanggal_mulai'  => 'required|date',
            'tanggal_selesai'=> 'required|date|after_or_equal:tanggal_mulai',
            'person_per_day' => 'required|integer|min:1'
        ]);

        try {
            $service = new \App\Modules\ManajemenAsetDanAsrama\Services\JadwalPiketService();
            $generated = $service->generateForKamar(
                $request->kamar_id,
                $request->tanggal_mulai,
                $request->tanggal_selesai,
                $request->person_per_day
            );

            if ($generated === 0) {
                return redirect()->back()->with('warning', 'Tidak ada jadwal yang di-generate (Mungkin tidak ada santri di kamar tersebut, atau jadwal di tanggal tersebut sudah penuh).');
            }

            return redirect()->back()->with('success', "Berhasil me-generate {$generated} jadwal piket.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
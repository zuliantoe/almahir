<?php

namespace Modules\Akademik\Controllers;

use App\Http\Requests\AkademikRequest\StoreKalenderAkademikRequest;
use App\Http\Requests\AkademikRequest\UpdateKalenderAkademikRequest;
use App\Modules\Akademik\Models\KalenderAkademik;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Akademik\Models\JenisKegiatan;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class KalenderAkademikController extends Controller
{
    public function index(Request $request)
    {
        $kalenderAkademik = KalenderAkademik::query()
            ->with(['tahunAjaran', 'jenisKegiatan'])
            ->when($request->filled('tahunajaran_id'), function ($query) use ($request) {
                $query->where('tahunajaran_id', $request->tahunajaran_id);
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('nama_kegiatan', 'like', '%' . $request->search . '%');
            })
            ->orderByDesc('tanggal_awal')
            ->paginate(10)
            ->withQueryString();

        $tahunAjarans = TahunAjaran::all();
        return view('akademik::kalender-akademik.index', compact('kalenderAkademik', 'tahunAjarans'));
    }

    public function create()
    {
        $tahunAjarans = TahunAjaran::all();
        $jenisKegiatans = JenisKegiatan::all();
        return view('akademik::kalender-akademik.create', compact('tahunAjarans', 'jenisKegiatans'));
    }

    public function store(StoreKalenderAkademikRequest $request)
    {
        try {
            KalenderAkademik::create($request->validated());

            return redirect()
                ->route('akademik.kalender-akademik.index')
                ->with('success', 'Kegiatan kalender akademik berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan data: ' . $e->getMessage());
        }
    }

    public function show(KalenderAkademik $kalenderAkademik)
    {
        $kalenderAkademik->load(['tahunAjaran', 'jenisKegiatan']);
        return view('akademik::kalender-akademik.show', compact('kalenderAkademik'));
    }

    public function edit(KalenderAkademik $kalenderAkademik)
    {
        $tahunAjarans = TahunAjaran::all();
        $jenisKegiatans = JenisKegiatan::all();
        return view('akademik::kalender-akademik.edit', compact('kalenderAkademik', 'tahunAjarans', 'jenisKegiatans'));
    }

    public function update(UpdateKalenderAkademikRequest $request, KalenderAkademik $kalenderAkademik)
    {
        try {
            $kalenderAkademik->update($request->validated());

            return redirect()
                ->route('akademik.kalender-akademik.index')
                ->with('success', 'Kegiatan kalender akademik berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    public function destroy(KalenderAkademik $kalenderAkademik)
    {
        try {
            $kalenderAkademik->delete();

            return redirect()
                ->route('akademik.kalender-akademik.index')
                ->with('success', 'Kegiatan kalender akademik berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()
                ->route('akademik.kalender-akademik.index')
                ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}

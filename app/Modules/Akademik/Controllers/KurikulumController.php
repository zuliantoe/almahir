<?php

namespace Modules\Akademik\Controllers;

use App\Http\Requests\AkademikRequest\StoreKurikulumRequest;
use App\Http\Requests\AkademikRequest\UpdateKurikulumRequest;
use App\Modules\Akademik\Models\Kurikulum;
use App\Modules\Akademik\Models\MasterKurikulum;
use App\Modules\Akademik\Models\Tingkat;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Akademik\Models\Kelas;
use App\Modules\Akademik\Models\MataPelajaran;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class KurikulumController extends Controller
{
    public function index(Request $request)
    {
        $kurikulum = Kurikulum::query()
            ->with(['masterKurikulum', 'tingkat', 'tahunAjaran', 'kelas', 'mataPelajaran'])
            ->when($request->filled('master_kurikulum_id'), function ($query) use ($request) {
                $query->where('master_kurikulum_id', $request->master_kurikulum_id);
            })
            ->when($request->filled('tingkat_id'), function ($query) use ($request) {
                $query->where('tingkat_id', $request->tingkat_id);
            })
            ->orderByDesc('tahunajaran_id')
            ->orderBy('tingkat_id')
            ->paginate(10)
            ->withQueryString();

        $masterKurikulums = MasterKurikulum::all();
        $tingkats = Tingkat::all();
        return view('akademik::kurikulum.index', compact('kurikulum', 'masterKurikulums', 'tingkats'));
    }

    public function create()
    {
        $masterKurikulums = MasterKurikulum::all();
        $tingkats = Tingkat::all();
        $tahunAjarans = TahunAjaran::all();
        $kelases = Kelas::all();
        $mapels = MataPelajaran::all();

        return view('akademik::kurikulum.create', compact('masterKurikulums', 'tingkats', 'tahunAjarans', 'kelases', 'mapels'));
    }

    public function store(StoreKurikulumRequest $request)
    {
        try {
            Kurikulum::create($request->validated());

            return redirect()
                ->route('akademik.kurikulum.index')
                ->with('success', 'Data kurikulum berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan data: ' . $e->getMessage());
        }
    }

    public function show(Kurikulum $kurikulum)
    {
        $kurikulum->load(['masterKurikulum', 'tingkat', 'tahunAjaran', 'kelas', 'mataPelajaran']);
        return view('akademik::kurikulum.show', compact('kurikulum'));
    }

    public function edit(Kurikulum $kurikulum)
    {
        $masterKurikulums = MasterKurikulum::all();
        $tingkats = Tingkat::all();
        $tahunAjarans = TahunAjaran::all();
        $kelases = Kelas::all();
        $mapels = MataPelajaran::all();

        return view('akademik::kurikulum.edit', compact('kurikulum', 'masterKurikulums', 'tingkats', 'tahunAjarans', 'kelases', 'mapels'));
    }

    public function update(UpdateKurikulumRequest $request, Kurikulum $kurikulum)
    {
        try {
            $kurikulum->update($request->validated());

            return redirect()
                ->route('akademik.kurikulum.index')
                ->with('success', 'Data kurikulum berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    public function destroy(Kurikulum $kurikulum)
    {
        try {
            $kurikulum->delete();

            return redirect()
                ->route('akademik.kurikulum.index')
                ->with('success', 'Data kurikulum berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()
                ->route('akademik.kurikulum.index')
                ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}

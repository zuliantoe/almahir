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
                ->route('akademik.kurikulum.create')
                ->with('success', 'Data kurikulum berhasil ditambahkan. Silakan input data berikutnya.');
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

    /**
     * Bulk Store multiple curriculum entries
     */
    public function bulkStore(Request $request)
    {
        $maxJamSeminggu = \App\Modules\Akademik\Models\MasterJamPelajaran::where('is_istirahat', false)->count() ?: 48;
        $request->validate([
            'master_kurikulum_id' => 'required|exists:master_kurikulum,id',
            'tingkat_id' => 'required|exists:tingkat,id',
            'tahunajaran_id' => 'required|exists:tahun_ajaran,id',
            'kelas_id' => 'nullable|exists:kelas,id',
            'details' => 'required|array|min:1',
            'details.*.mapel_id' => 'required|exists:mata_pelajaran,id',
            'details.*.total_jam_minggu' => 'required|integer|max:' . $maxJamSeminggu,
            'details.*.kkm' => 'required|numeric|between:0,100',
        ]);

        $kelasId = $request->kelas_id;
        $mapelIds = collect($request->details)->pluck('mapel_id')->toArray();
        $newSum = collect($request->details)->sum('total_jam_minggu');

        $existingSum = Kurikulum::where('master_kurikulum_id', $request->master_kurikulum_id)
            ->where('tingkat_id', $request->tingkat_id)
            ->where('tahunajaran_id', $request->tahunajaran_id)
            ->where(function ($query) use ($kelasId) {
                if ($kelasId === null || $kelasId === '') {
                    $query->whereNull('kelas_id');
                } else {
                    $query->where('kelas_id', $kelasId);
                }
            })
            ->whereNotIn('mapel_id', $mapelIds)
            ->sum('totaljam');

        if (($existingSum + $newSum) > $maxJamSeminggu) {
            return redirect()->back()->withInput()->with('error', "Gagal simpan massal: Total akumulasi jam pelajaran per minggu untuk kurikulum ini tidak boleh melebihi {$maxJamSeminggu} jam pelajaran. (Saat ini sudah terisi {$existingSum} jam di database untuk mata pelajaran lain, dan yang Anda input baru berjumlah {$newSum} jam).");
        }

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $commonData = $request->only(['master_kurikulum_id', 'tingkat_id', 'tahunajaran_id', 'kelas_id']);
            $count = 0;

            foreach ($request->details as $detail) {
                Kurikulum::updateOrCreate(
                    array_merge($commonData, ['mapel_id' => $detail['mapel_id']]),
                    [
                        'totaljam' => $detail['total_jam_minggu'] ?? $detail['totaljam'] ?? 0,
                        'kkm' => $detail['kkm'],
                    ]
                );
                $count++;
            }

            \Illuminate\Support\Facades\DB::commit();
            return redirect()->route('akademik.kurikulum.index')
                ->with('success', "Berhasil menyimpan $count data kurikulum sekaligus.");
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal simpan massal: ' . $e->getMessage());
        }
    }
}

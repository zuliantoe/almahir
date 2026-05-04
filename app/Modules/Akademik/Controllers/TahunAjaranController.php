<?php

namespace Modules\Akademik\Controllers;

use App\Http\Requests\AkademikRequest\StoreTahunAjaranRequest as AkademikRequestStoreTahunAjaranRequest;
use App\Http\Requests\AkademikRequest\UpdateTahunAjaranRequest as AkademikRequestUpdateTahunAjaranRequest;
use App\Modules\Akademik\Models\TahunAjaran;
use Illuminate\Http\Request;

use Illuminate\Routing\Controller;

class TahunAjaranController extends Controller
{
    public function index(Request $request)
    {
        $tahunAjaran = TahunAjaran::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('tahunajaran', 'like', '%' . $request->search . '%');
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->orderByDesc('status')
            ->orderByDesc('tahunajaran')
            ->paginate(10)
            ->withQueryString();
        // Gunakan namespace 'akademik'::view
        return view('akademik::tahun-ajaran.index', compact('tahunAjaran'));
    }

    public function create()
    {
        return view('akademik::tahun-ajaran.create');
    }

    public function store(AkademikRequestStoreTahunAjaranRequest $request)
    {
        try {
            $data = $request->validated();
            $data['status'] = $request->boolean('status');

            // Jika status aktif, nonaktifkan yang lain
            if ($data['status']) {
                TahunAjaran::where('status', 1)->update(['status' => 0]);
            }

            TahunAjaran::create($data);

            return redirect()
                ->route('akademik.tahun-ajaran.index')
                ->with('success', 'Tahun ajaran berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan data: ' . $e->getMessage());
        }
    }

    public function show(TahunAjaran $tahunAjaran)
    {
        return view('akademik::tahun-ajaran.show', compact('tahunAjaran'));
    }

    public function edit(TahunAjaran $tahunAjaran)
    {
        return view('akademik::tahun-ajaran.edit', compact('tahunAjaran'));
    }

    public function update(AkademikRequestUpdateTahunAjaranRequest $request, TahunAjaran $tahunAjaran)
    {
        $data = $request->validated();
        $data['status'] = $request->boolean('status');

        if ($data['status']) {
            TahunAjaran::where('status', 1)
                ->where('id', '!=', $tahunAjaran->id)
                ->update(['status' => 0]);
        }

        $tahunAjaran->update($data);

        return redirect()
            ->route('akademik.tahun-ajaran.index')
            ->with('success', 'Tahun ajaran berhasil diperbarui.');
    }

    public function destroy(TahunAjaran $tahunAjaran)
    {
        if ($tahunAjaran->status) {
            return redirect()
                ->route('akademik.tahun-ajaran.index')
                ->with('error', 'Tahun ajaran aktif tidak dapat dihapus.');
        }

        if ($tahunAjaran->kalenderAkademik()->exists() || $tahunAjaran->jadwalPelajaran()->exists() || $tahunAjaran->kurikulum()->exists() || $tahunAjaran->rombel()->exists()) {
            return redirect()
                ->route('akademik.tahun-ajaran.index')
                ->with('error', 'Data tidak dapat dihapus karena masih digunakan (Kalender Akademik, Jadwal Pelajaran, Kurikulum, atau Rombel).');
        }

        $tahunAjaran->delete();

        return redirect()
            ->route('akademik.tahun-ajaran.index')
            ->with('success', 'Tahun ajaran berhasil dihapus.');
    }
}

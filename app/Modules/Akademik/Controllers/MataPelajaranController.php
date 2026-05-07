<?php

namespace Modules\Akademik\Controllers;

// use App\Http\Controllers\Controller;

use App\Http\Controllers\Controller;
use App\Modules\Akademik\Models\MataPelajaran;
use App\Modules\Akademik\Models\KategoriPelajaran;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MataPelajaranController extends Controller
{
    public function index(Request $request)
    {
        $query = MataPelajaran::with('kategori');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        // Filter kategori
        if ($request->filled('kategori')) {
            $query->where('kategori_id', $request->kategori);
        }

        $mataPelajaran = $query
            ->orderBy('kode', 'asc')
            ->paginate(10)
            ->withQueryString();

        $kategoriList = KategoriPelajaran::orderBy('kategori')->get();

        return view('akademik::mata-pelajaran.index', compact(
            'mataPelajaran',
            'kategoriList'
        ));
    }

    public function create()
    {
        $kategoriList = KategoriPelajaran::orderBy('kategori')->get();
        return view('akademik::mata-pelajaran.create', compact('kategoriList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|max:50|unique:mata_pelajaran,kode',
            'nama' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori_pelajaran,id',
        ]);

        MataPelajaran::create($request->only([
            'kode',
            'nama',
            'kategori_id'
        ]));

        return redirect()->route('akademik.mata-pelajaran.index')
            ->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function show(MataPelajaran $mataPelajaran)
    {
        $mataPelajaran->load('kategori');
        return view('akademik::mata-pelajaran.show', compact('mataPelajaran'));
    }

    public function edit(MataPelajaran $mataPelajaran)
    {
        $kategoriList = KategoriPelajaran::orderBy('kategori')->get();
        return view('akademik::mata-pelajaran.edit', compact(
            'mataPelajaran',
            'kategoriList'
        ));
    }

    public function update(Request $request, MataPelajaran $mataPelajaran)
    {
        $request->validate([
            'kode' => [
                'required',
                'string',
                'max:50',
                Rule::unique('mata_pelajaran', 'kode')
                    ->ignore($mataPelajaran->id),
            ],
            'nama' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori_pelajaran,id',
        ]);

        $mataPelajaran->update($request->only([
            'kode',
            'nama',
            'kategori_id'
        ]));

        return redirect()->route('akademik.mata-pelajaran.index')
            ->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    public function destroy(MataPelajaran $mataPelajaran)
    {
        if ($mataPelajaran->jadwalPelajaran()->exists() || $mataPelajaran->kurikulum()->exists()) {
            return redirect()->route('akademik.mata-pelajaran.index')
                ->with('error', 'Mata pelajaran tidak dapat dihapus karena masih digunakan dalam jadwal atau kurikulum.');
        }

        $mataPelajaran->delete();

        return redirect()->route('akademik.mata-pelajaran.index')
            ->with('success', 'Mata pelajaran berhasil dihapus.');
    }

    /**
     * Import Mata Pelajaran from CSV
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:2048'
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');
        
        // Skip header
        fgetcsv($handle);

        $imported = 0;
        $skipped = 0;

        while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
            // Data format: kode, nama, kategori_id
            if (count($data) >= 3) {
                MataPelajaran::updateOrCreate(
                    ['kode' => $data[0]],
                    [
                        'nama' => $data[1],
                        'kategori_id' => $data[2]
                    ]
                );
                $imported++;
            } else {
                $skipped++;
            }
        }

        fclose($handle);

        return redirect()->route('akademik.mata-pelajaran.index')
            ->with('success', "Berhasil mengimpor $imported data. (Gagal: $skipped)");
    }

    /**
     * Bulk Store Mata Pelajaran
     */
    public function bulkStore(Request $request)
    {
        $request->validate([
            'subjects' => 'required|array|min:1',
            'subjects.*.kode' => 'required|string|max:50',
            'subjects.*.nama' => 'required|string|max:255',
            'subjects.*.kategori_id' => 'required|exists:kategori_pelajaran,id',
        ]);

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $count = 0;
            foreach ($request->subjects as $subject) {
                MataPelajaran::updateOrCreate(
                    ['kode' => $subject['kode']],
                    [
                        'nama' => $subject['nama'],
                        'kategori_id' => $subject['kategori_id']
                    ]
                );
                $count++;
            }

            \Illuminate\Support\Facades\DB::commit();
            return redirect()->route('akademik.mata-pelajaran.index')
                ->with('success', "Berhasil menyimpan $count mata pelajaran sekaligus.");
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal simpan massal: ' . $e->getMessage());
        }
    }
}

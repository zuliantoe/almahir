<?php

namespace App\Modules\ManajemenAsetDanAsrama\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Modules\ManajemenAsetDanAsrama\Models\Aset;

use App\Modules\ManajemenAsetDanAsrama\Traits\HasSoftDeleteWithUser;
use App\Modules\ManajemenAsetDanAsrama\Traits\HasAssetCode;

class AsetController extends BaseController
{
    use HasSoftDeleteWithUser, HasAssetCode;

    public function index(Request $request): View
    {
        $search = $request->input('search');

        $query = Aset::with('pengadaan:id,nomor_po,pengajuan_id', 'pengadaan.pengajuan:id,nomor_pengajuan')
                    ->whereNull('deleted_at');

        if (!empty($search)) {
            $terms = array_filter(explode(' ', $search));
            $query->where(function($q) use ($terms) {
                // Cari di kode_aset
                $q->where(function($qCode) use ($terms) {
                    foreach ($terms as $term) {
                        $qCode->where('kode_aset', 'LIKE', '%' . $term . '%');
                    }
                })
                // Atau cari di nama_aset
                ->orWhere(function($qName) use ($terms) {
                    foreach ($terms as $term) {
                        $qName->where('nama_aset', 'LIKE', '%' . $term . '%');
                    }
                });
            });
        }

        $aset = $query->latest()->paginate(15)->appends(['search' => $search]);
        
        $stats = [
            'total'           => Aset::whereNull('deleted_at')->count(),
            'baik'            => Aset::whereNull('deleted_at')->where('status_kondisi', 'baik')->count(),
            'rusak'           => Aset::whereNull('deleted_at')->where('status_kondisi', 'rusak')->count(),
            'dalam_perbaikan' => Aset::whereNull('deleted_at')->where('status_kondisi', 'dalam_perbaikan')->count(),
        ];
        
        return view('manajemenasetdanasrama::aset.index', [
            'title' => 'Master Aset',
            'aset'  => $aset,
            'stats' => $stats,
        ]);
    }

    public function scan(): View
    {
        return view('manajemenasetdanasrama::aset.scan', [
            'title' => 'Scan QR Aset',
        ]);
    }

    /**
     * Find asset by code and return its detail URL.
     */
    public function findByCode(Request $request)
    {
        $code = $request->input('code');
        $aset = Aset::where('kode_aset', $code)->first();

        if ($aset) {
            return response()->json([
                'success' => true,
                'url' => route('manajemenasetdanasrama.aset.show', $aset->id)
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Aset dengan kode ' . $code . ' tidak ditemukan.'
        ]);
    }

    /**
     * Show the form for creating a new asset manually.
     */
    public function create(): View
    {
        return view('manajemenasetdanasrama::aset.create', [
            'title' => 'Tambah Aset Langsung',
        ]);
    }

    /**
     * Store a newly created asset in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_aset'       => 'required|string|max:255',
            'harga'           => 'nullable|numeric|min:0',
            'tanggal_pengadaan' => 'required|date',
            'kondisi'         => 'nullable|string',
            'deskripsi_aset'  => 'nullable|string',
            'jumlah_aset'     => 'required|integer|min:1|max:500',
        ]);

        $jumlah = $validated['jumlah_aset'];
        unset($validated['jumlah_aset']);

        $validated['status_kondisi'] = 'baik'; 

        for ($i = 0; $i < $jumlah; $i++) {
            $data = $validated;
            $data['kode_aset'] = $this->generateAssetCode($request->nama_aset, Aset::class, 'kode_aset');
            Aset::create($data);
        }

        if ($jumlah > 1) {
            return redirect()->route('manajemenasetdanasrama.aset.index')
                ->with('success', "Berhasil menambahkan {$jumlah} aset baru secara langsung.");
        }

        return redirect()->route('manajemenasetdanasrama.aset.index')
            ->with('success', 'Aset berhasil ditambahkan dengan kode otomatis: ' . $data['kode_aset']);
    }

    /**
     * Display the specified aset.
     */
    public function show(string $id): View
    {
        $aset = Aset::with(['pengadaan.pengajuan', 'kerusakan', 'pemeliharaan'])
                    ->findOrFail($id);
        
        return view('manajemenasetdanasrama::aset.show', [
            'title' => 'Detail Aset',
            'aset'  => $aset,
        ]);
    }

    /**
     * Show the form for editing the specified aset.
     */
    public function edit(string $id): View
    {
        $aset = Aset::findOrFail($id);
        
        return view('manajemenasetdanasrama::aset.edit', [
            'title' => 'Edit Aset',
            'aset'  => $aset,
        ]);
    }

    /**
     * Update the specified aset in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $aset = Aset::findOrFail($id);
        
        $validated = $request->validate([
            'nama_aset'       => 'required|string|max:255',
            'kondisi'         => 'nullable|string',
            'deskripsi_aset'  => 'nullable|string',
        ]);

        $aset->update($validated);

        return redirect()->route('manajemenasetdanasrama.aset.index')
            ->with('success', 'Data aset berhasil diperbarui.');
    }

    /**
     * Remove the specified aset from storage (soft delete).
     */
    public function destroy(Request $request, string $id): RedirectResponse
    {
        $aset = Aset::findOrFail($id);
        
        $this->performSoftDelete($request, $aset);

        return redirect()->route('manajemenasetdanasrama.aset.index')
            ->with('success', 'Aset berhasil dipindahkan ke trash.');
    }

    /**
     * Restore the specified asset from trash.
     */
    public function restore(string $id): RedirectResponse
    {
        $aset = Aset::onlyTrashed()->findOrFail($id);
        $aset->restore();

        return redirect()->route('manajemenasetdanasrama.trash.index')
            ->with('success', 'Aset berhasil dikembalikan.');
    }

    /**
     * Print asset labels with QR Code.
     */
    public function printLabel(Request $request)
    {
        $ids = $request->input('ids');
        if ($ids) {
            $idsArray = explode(',', $ids);
            $aset = Aset::with('kamar')->whereIn('id', $idsArray)->get();
        } else {
            // Jika tidak ada ID, mungkin dari halaman index tanpa seleksi (print all filtered?)
            // Untuk sekarang, kita support yang ada ID-nya saja atau satu ID
            $id = $request->input('id');
            if ($id) {
                $aset = Aset::with('kamar')->where('id', $id)->get();
            } else {
                return redirect()->back()->with('error', 'Pilih aset yang ingin dicetak labelnya.');
            }
        }

        return view('manajemenasetdanasrama::aset.print-label', [
            'title' => 'Cetak Label Aset',
            'aset'  => $aset
        ]);
    }

    /**
     * Bulk destroy assets based on pattern.
     */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $request->validate([
            'pattern' => 'required|string|min:2',
        ]);

        $pattern = strtoupper($request->pattern);
        
        // Query untuk mencari aset berdasarkan kode (exact atau prefix)
        $query = Aset::where('kode_aset', 'LIKE', "{$pattern}%");
        $count = $query->count();

        if ($count === 0) {
            return redirect()->back()->with('error', "Tidak ditemukan aset dengan pola kode '{$pattern}'.");
        }

        // Simpan jumlah untuk pesan sukses
        $query->delete();

        return redirect()->route('manajemenasetdanasrama.aset.index')
            ->with('success', "Berhasil menghapus {$count} aset dengan pola kode '{$pattern}'.");
    }

    /**
     * Duplicate an existing asset.
     */
    public function duplicate(Request $request, string $id): RedirectResponse
    {
        $request->validate([
            'jumlah' => 'required|integer|min:1|max:100'
        ]);

        $original = Aset::findOrFail($id);
        $jumlah = (int) $request->jumlah;

        for ($i = 0; $i < $jumlah; $i++) {
            $new = $original->replicate();
            
            // Ambil tanggal dari kode asli biar tetep identik kelompoknya
            $parts = explode('-', $original->kode_aset);
            $originalDate = isset($parts[1]) ? $parts[1] : null;

            $new->kode_aset = $this->generateAssetCode($original->nama_aset, Aset::class, 'kode_aset', $originalDate);
            $new->status_kondisi = 'baik';
            $new->save();
        }

        return redirect()->route('manajemenasetdanasrama.aset.index')
            ->with('success', "Berhasil menduplikat aset sebanyak {$jumlah} kali dengan kode berurutan otomatis.");
    }

    /**
     * Suggest an asset code via AJAX.
     */
    public function suggestCode(Request $request)
    {
        $nama = $request->input('nama', 'AST');
        $code = $this->generateAssetCode($nama, Aset::class, 'kode_aset');
        
        return response()->json(['code' => $code]);
    }
    /**
     * Process bulk print request from prefixes/patterns.
     */
    public function bulkPrintAction(Request $request)
    {
        $request->validate([
            'patterns' => 'required|array',
            'patterns.*' => 'required|string|min:1'
        ]);

        $patterns = $request->input('patterns');
        $query = Aset::query();

        $query->where(function($q) use ($patterns) {
            foreach ($patterns as $pattern) {
                $q->orWhere('kode_aset', 'LIKE', trim(strtoupper($pattern)) . '%');
            }
        });

        $aset = $query->get();

        if ($aset->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada aset yang ditemukan dengan pola kode tersebut.');
        }

        $ids = $aset->pluck('id')->implode(',');

        return redirect()->route('manajemenasetdanasrama.aset.print-label', ['ids' => $ids]);
    }
}
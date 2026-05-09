<?php

namespace App\Modules\ManajemenAsetDanAsrama\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Modules\ManajemenAsetDanAsrama\Models\PengadaanAset;
use App\Modules\ManajemenAsetDanAsrama\Models\PengajuanAset;
use App\Modules\ManajemenAsetDanAsrama\Models\Aset;

use App\Modules\ManajemenAsetDanAsrama\Traits\HasNomorOtomatis;
use App\Modules\ManajemenAsetDanAsrama\Traits\HasAssetCode;

class PengadaanController extends BaseController
{
    use HasNomorOtomatis, HasAssetCode;

    /**
     * Display a listing of pengadaan aset.
     */
    public function index(Request $request): View
    {
        $pengadaan = PengadaanAset::with(['pengajuan', 'aset'])
                        ->where('status', 'dipesan') // Cuma tampilin yang masih dipesan
                        ->latest()
                        ->paginate(10);
        
        // Pengajuan yang sudah disetujui tapi belum dibuatkan pengadaan
        $menungguProses = PengajuanAset::with('pengaju:id,name')
                            ->where('status', 'disetujui')
                            ->latest()
                            ->get();

        $kamar = \App\Modules\ManajemenAsetDanAsrama\Models\Kamar::all();

        $stats = [
            'menunggu' => $menungguProses->count(),
            'dipesan'  => PengadaanAset::where('status', 'dipesan')->count(),
            'datang'   => PengadaanAset::where('status', 'datang')->count(),
            'total_biaya' => PengadaanAset::sum('biaya_riil'),
        ];

        return view('manajemenasetdanasrama::pengadaan.index', [
            'title'          => 'Data Pengadaan Aset',
            'pengadaan'      => $pengadaan,
            'menungguProses' => $menungguProses,
            'kamar'          => $kamar,
            'stats'          => $stats,
        ]);
    }

    /**
     * Show the form for processing pengadaan aset.
     */
    public function proses(string $id): View
    {
        $pengajuan = PengajuanAset::with('pengaju')->findOrFail($id);
        
        return view('manajemenasetdanasrama::pengadaan.proses', [
            'title'     => 'Proses Pengadaan Aset',
            'pengajuan' => $pengajuan,
        ]);
    }

    /**
     * Store a newly created pengadaan aset in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'pengajuan_id'    => 'required|exists:pengajuan_aset,id|unique:pengadaan_aset,pengajuan_id',
            'vendor'          => 'required|string|max:255',
            'tanggal_pesan'   => 'required|date',
            'estimasi_datang' => 'required|date|after_or_equal:tanggal_pesan',
            'biaya_riil'      => 'required|numeric|min:0',
            'catatan_pengadaan' => 'nullable|string',
        ]);

        $data = $validated;
        $data['nomor_po'] = $this->generateNomor(PengadaanAset::class, 'PO');
        $data['status'] = 'dipesan';

        PengadaanAset::create($data);

        // Update status pengajuan
        $pengajuan = PengajuanAset::find($request->pengajuan_id);
        $pengajuan->status = 'proses_pengadaan';
        $pengajuan->save();

        return redirect()->route('manajemenasetdanasrama.pengadaan.index')
            ->with('success', 'Data pengadaan berhasil disimpan.');
    }

    /**
     * Mark pengadaan as completed and create aset.
     */
    public function selesai(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'tanggal_datang'  => 'required|date',
            'nama_aset'       => 'required|string|max:255',
            'kondisi'         => 'nullable|string',
            'deskripsi_aset'  => 'nullable|string',
        ]);

        $pengadaan = PengadaanAset::with('pengajuan')->findOrFail($id);
        
        if ($pengadaan->status === 'datang') {
            return redirect()->route('manajemenasetdanasrama.pengadaan.index')
                ->with('error', 'Status pengadaan ini sudah selesai (Barang Datang), tidak dapat diupdate lagi.');
        }

        // Update status pengadaan
        $pengadaan->tanggal_datang = $request->tanggal_datang;
        $pengadaan->status = 'datang';
        $pengadaan->save();

        // Generate kode otomatis
        $kode_aset = $this->generateAssetCode($request->nama_aset, Aset::class, 'kode_aset');

        // Buat aset baru
        Aset::create([
            'kode_aset'          => $kode_aset,
            'nama_aset'          => $request->nama_aset,
            'tanggal_pengajuan'  => $pengadaan->pengajuan->tanggal_pengajuan,
            'harga'              => $pengadaan->biaya_riil,
            'status_kondisi'     => 'baik',
            'tanggal_pengadaan'  => $request->tanggal_datang,
            'kondisi'            => $request->kondisi,
            'deskripsi_aset'     => $request->deskripsi_aset,
            'pengadaan_id'       => $pengadaan->id,
            'kamar_id'           => null, // Lokasi dihilangkan sesuai request
        ]);

        return redirect()->route('manajemenasetdanasrama.aset.index')
            ->with('success', 'Barang berhasil diterima dan didaftarkan ke Master Aset dengan kode: ' . $kode_aset);
    }

    /**
     * Bulk confirm receipt of items by name prefix.
     */
    public function bulkConfirm(Request $request): RedirectResponse
    {
        $request->validate([
            'prefix' => 'required|string|min:1',
            'tanggal_datang' => 'required|date',
            'kondisi' => 'nullable|string',
            'deskripsi_aset' => 'nullable|string',
        ]);

        $prefix = strtoupper($request->prefix);
        $pengadaan = PengadaanAset::with('pengajuan')
                        ->where('status', 'dipesan')
                        ->where(function($q) use ($prefix) {
                            $q->where('nomor_po', 'LIKE', $prefix . '%')
                              ->orWhereHas('pengajuan', function($sq) use ($prefix) {
                                  $sq->where('nama_aset', 'LIKE', $prefix . '%')
                                     ->orWhere('nomor_pengajuan', 'LIKE', $prefix . '%');
                              });
                        })
                        ->get();

        if ($pengadaan->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data pengadaan (dipesan) yang ditemukan dengan inisial tersebut.');
        }

        $count = 0;
        foreach ($pengadaan as $item) {
            /** @var PengadaanAset $item */
            // Update Pengadaan
            $item->tanggal_datang = $request->tanggal_datang;
            $item->status = 'datang';
            $item->save();

            // Create Aset
            $nama_aset = $item->pengajuan->nama_aset;
            $kode_aset = $this->generateAssetCode($nama_aset, Aset::class, 'kode_aset', \Carbon\Carbon::parse($request->tanggal_datang)->format('dmy'));

            Aset::create([
                'kode_aset'          => $kode_aset,
                'nama_aset'          => $nama_aset,
                'tanggal_pengajuan'  => $item->pengajuan->tanggal_pengajuan,
                'harga'              => $item->biaya_riil,
                'status_kondisi'     => 'baik',
                'tanggal_pengadaan'  => $request->tanggal_datang,
                'kondisi'            => $request->kondisi,
                'deskripsi_aset'     => $request->deskripsi_aset,
                'pengadaan_id'       => $item->id,
            ]);
            $count++;
        }

        return redirect()->route('manajemenasetdanasrama.aset.index')
            ->with('success', "$count barang berhasil dikonfirmasi datang dan didaftarkan ke Master Aset.");
    }

    /**
     * Bulk store procurement (Create POs in batch).
     */
    public function bulkStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'prefix'          => 'required|string|min:1',
            'vendor'          => 'required|string|max:255',
            'tanggal_pesan'   => 'required|date',
            'estimasi_datang' => 'required|date|after_or_equal:tanggal_pesan',
            'biaya_riil'      => 'nullable|numeric|min:0',
            'catatan_pengadaan' => 'nullable|string',
        ]);

        $prefix = strtoupper($request->prefix);
        $pengajuan = PengajuanAset::where('status', 'disetujui')
                        ->where(function($q) use ($prefix) {
                            $q->where('nama_aset', 'LIKE', $prefix . '%')
                              ->orWhere('nomor_pengajuan', 'LIKE', $prefix . '%');
                        })
                        ->get();

        if ($pengajuan->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada pengajuan disetujui yang ditemukan dengan inisial tersebut.');
        }

        $count = 0;
        foreach ($pengajuan as $item) {
            /** @var PengajuanAset $item */
            // Cek apakah sudah ada pengadaan (safety)
            if (PengadaanAset::where('pengajuan_id', $item->id)->exists()) {
                continue;
            }

            $data = $validated;
            unset($data['prefix']);
            $data['pengajuan_id'] = $item->id;
            $data['nomor_po'] = $this->generateNomor(PengadaanAset::class, 'PO');
            $data['status'] = 'dipesan';
            
            // LOGIKA HARGA: Jika input biaya_riil kosong, pake estimasi_harga dari pengajuan
            if (empty($request->biaya_riil)) {
                $data['biaya_riil'] = $item->estimasi_harga;
            }

            PengadaanAset::create($data);

            // Update status pengajuan
            $item->status = 'proses_pengadaan';
            $item->save();
            $count++;
        }

        return redirect()->route('manajemenasetdanasrama.pengadaan.index')
            ->with('success', "$count data pengadaan berhasil diproses secara masal.");
    }
}
<?php

namespace App\Modules\ManajemenAsetDanAsrama\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Modules\ManajemenAsetDanAsrama\Models\PengadaanAset;
use App\Modules\ManajemenAsetDanAsrama\Models\PengajuanAset;
use App\Modules\ManajemenAsetDanAsrama\Models\Aset;

use App\Modules\ManajemenAsetDanAsrama\Traits\HasNomorOtomatis;

class PengadaanController extends BaseController
{
    use HasNomorOtomatis;

    /**
     * Display a listing of pengadaan aset.
     */
    public function index(Request $request): View
    {
        $pengadaan = PengadaanAset::with('pengajuan:id,nomor_pengajuan,nama_aset')
                        ->whereHas('pengajuan')
                        ->latest()
                        ->paginate(15);
        
        // Pengajuan yang sudah disetujui tapi belum dibuatkan pengadaan
        $menungguProses = PengajuanAset::with('pengaju:id,nama')
                            ->where('status', 'disetujui')
                            ->latest()
                            ->get();

        return view('manajemenasetdanasrama::pengadaan.index', [
            'title'          => 'Data Pengadaan Aset',
            'pengadaan'      => $pengadaan,
            'menungguProses' => $menungguProses,
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
            'estimasi_datang' => 'required|date|after:tanggal_pesan',
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
            'kode_aset'       => 'required|string|unique:aset,kode_aset',
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

        // Buat aset baru
        Aset::create([
            'kode_aset'          => $request->kode_aset,
            'nama_aset'          => $request->nama_aset,
            'tanggal_pengajuan'  => $pengadaan->pengajuan->tanggal_pengajuan,
            'harga'              => $pengadaan->biaya_riil,
            'status_kondisi'     => 'baik',
            'tanggal_pengadaan'  => $request->tanggal_datang,
            'kondisi'            => $request->kondisi,
            'deskripsi_aset'     => $request->deskripsi_aset,
            'pengadaan_id'       => $pengadaan->id,
        ]);

        return redirect()->route('manajemenasetdanasrama.aset.index')
            ->with('success', 'Aset berhasil ditambahkan ke master aset.');
    }
}
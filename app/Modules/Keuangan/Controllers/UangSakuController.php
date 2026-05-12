<?php

namespace Modules\Keuangan\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

use Modules\Keuangan\Models\UangSaku;
use Modules\Keuangan\Models\Pemasukan;
use Modules\Keuangan\Models\Sumber;
use Modules\Keuangan\Models\Pengeluaran;
use Modules\Keuangan\Models\Tujuan;
use Modules\Siswa\Models\Siswa;
use Carbon\Carbon;

class UangSakuController extends Controller
{
    public function index(Request $request): View
    {
        $uangsakus = UangSaku::with('siswa')->get();
        return view('keuangan::uangsakus.index', compact('uangsakus'));
    }

    public function create(): View
    {
        $siswas = Siswa::where('status', 'aktif')->with('kelas.tingkat')->get();
        return view('keuangan::uangsakus.create', compact('siswas'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'siswa_id'  => 'required|exists:siswa,id',
            'jumlah'    => 'required|numeric|min:0',
            'tanggal'   => 'required|date',
            'status'    => 'required|string',
            'deskripsi' => 'nullable|string'
        ]);

        $uangsaku = UangSaku::create($request->all());

        $siswa = Siswa::with('kelas.tingkat')->find($uangsaku->siswa_id);
        $namaSiswa = $siswa->nama ?? 'Unknown';
        $tingkatSantri = isset($siswa->kelas->tingkat) ? "(" . $siswa->kelas->tingkat->nama_tingkat . ")" : "";
        $keterangan = $uangsaku->deskripsi ?: '-';
        $deskripsiKeuangan = "Uang Saku " . $namaSiswa . " " . $tingkatSantri . "\nKeterangan: " . $keterangan;

        if ($uangsaku->status === 'Belum Diterima Santri') {
            $sumber = Sumber::firstOrCreate(['nama' => 'Yayasan']);
            Pemasukan::create([
                'uang_saku_id' => $uangsaku->id,
                'sumber_id' => $sumber->id,
                'jumlah'    => $uangsaku->jumlah,
                'tanggal'   => $uangsaku->tanggal,
                'waktu'     => Carbon::now()->setTimezone('Asia/Jakarta')->format('H.i'),
                'deskripsi' => trim($deskripsiKeuangan)
            ]);
        } else {
            $tujuan = Tujuan::firstOrCreate(['nama' => 'Uang Saku']);
            Pengeluaran::create([
                'uang_saku_id' => $uangsaku->id,
                'tujuan_id' => $tujuan->id,
                'jumlah'    => $uangsaku->jumlah,
                'tanggal'   => $uangsaku->tanggal,
                'waktu'     => Carbon::now()->setTimezone('Asia/Jakarta')->format('H.i'),
                'deskripsi' => trim($deskripsiKeuangan)
            ]);
        }

        return redirect()->route('keuangan.uangsakus.index')->with('success', 'Uang Saku berhasil ditambahkan!');
    }

    public function show(string $id): View
    {
        $uangsaku = UangSaku::with('siswa')->findOrFail($id);
        return view('keuangan::uangsakus.show', compact('uangsaku'));
    }

    public function edit(string $id): View
    {
        $uangsaku = UangSaku::findOrFail($id);
        $siswas = Siswa::where('status', 'aktif')->with('kelas.tingkat')->get();
        return view('keuangan::uangsakus.edit', compact('uangsaku', 'siswas'));
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $request->validate([
            'siswa_id'  => 'required|exists:siswa,id',
            'jumlah'    => 'required|numeric|min:0',
            'tanggal'   => 'required|date',
            'status'    => 'required|string',
            'deskripsi' => 'nullable|string'
        ]);

        $uangsaku = UangSaku::findOrFail($id);
        $uangsaku->update($request->all());

        $siswa = Siswa::with('kelas.tingkat')->find($uangsaku->siswa_id);
        $namaSiswa = $siswa->nama ?? 'Unknown';
        $tingkatSantri = isset($siswa->kelas->tingkat) ? "(" . $siswa->kelas->tingkat->nama_tingkat . ")" : "";
        $keterangan = $uangsaku->deskripsi ?: '-';
        $deskripsiKeuangan = "Uang Saku " . $namaSiswa . " " . $tingkatSantri . "\nKeterangan: " . $keterangan;

        if ($uangsaku->status === 'Belum Diterima Santri') {
            // Remove from Pengeluaran if exists
            Pengeluaran::where('uang_saku_id', $uangsaku->id)->delete();

            // Update or Create Pemasukan
            $sumber = Sumber::firstOrCreate(['nama' => 'Yayasan']);
            Pemasukan::updateOrCreate(
                ['uang_saku_id' => $uangsaku->id],
                [
                    'sumber_id' => $sumber->id,
                    'jumlah'    => $uangsaku->jumlah,
                    'tanggal'   => $uangsaku->tanggal,
                    'waktu'     => Carbon::now()->setTimezone('Asia/Jakarta')->format('H.i'),
                    'deskripsi' => trim($deskripsiKeuangan)
                ]
            );
        } else {
            // Remove from Pemasukan if exists
            Pemasukan::where('uang_saku_id', $uangsaku->id)->delete();

            // Update or Create Pengeluaran
            $tujuan = Tujuan::firstOrCreate(['nama' => 'Uang Saku']);
            Pengeluaran::updateOrCreate(
                ['uang_saku_id' => $uangsaku->id],
                [
                    'tujuan_id' => $tujuan->id,
                    'jumlah'    => $uangsaku->jumlah,
                    'tanggal'   => $uangsaku->tanggal,
                    'waktu'     => Carbon::now()->setTimezone('Asia/Jakarta')->format('H.i'),
                    'deskripsi' => trim($deskripsiKeuangan)
                ]
            );
        }

        return redirect()->route('keuangan.uangsakus.index')->with('success', 'Uang Saku berhasil diperbarui!');
    }

    public function updateStatus(Request $request, string $id): RedirectResponse
    {
        $request->validate([
            'status' => 'required|string|in:Belum Diterima Santri,Sudah Diterima Santri'
        ]);

        $uangsaku = UangSaku::findOrFail($id);
        $uangsaku->update(['status' => $request->status]);

        $siswa = Siswa::with('kelas.tingkat')->find($uangsaku->siswa_id);
        $namaSiswa = $siswa->nama ?? 'Unknown';
        $tingkatSantri = isset($siswa->kelas->tingkat) ? "(" . $siswa->kelas->tingkat->nama_tingkat . ")" : "";
        $keterangan = $uangsaku->deskripsi ?: '-';
        $deskripsiKeuangan = "Uang Saku " . $namaSiswa . " " . $tingkatSantri . "\nKeterangan: " . $keterangan;

        if ($uangsaku->status === 'Belum Diterima Santri') {
            // Move to Pemasukan
            Pengeluaran::where('uang_saku_id', $uangsaku->id)->delete();
            
            $sumber = Sumber::firstOrCreate(['nama' => 'Yayasan']);
            Pemasukan::updateOrCreate(
                ['uang_saku_id' => $uangsaku->id],
                [
                    'sumber_id' => $sumber->id,
                    'jumlah'    => $uangsaku->jumlah,
                    'tanggal'   => $uangsaku->tanggal,
                    'waktu'     => Carbon::now()->setTimezone('Asia/Jakarta')->format('H.i'),
                    'deskripsi' => trim($deskripsiKeuangan)
                ]
            );
        } else {
            // Move to Pengeluaran
            Pemasukan::where('uang_saku_id', $uangsaku->id)->delete();

            $tujuan = Tujuan::firstOrCreate(['nama' => 'Uang Saku']);
            Pengeluaran::updateOrCreate(
                ['uang_saku_id' => $uangsaku->id],
                [
                    'tujuan_id' => $tujuan->id,
                    'jumlah'    => $uangsaku->jumlah,
                    'tanggal'   => $uangsaku->tanggal,
                    'waktu'     => Carbon::now()->setTimezone('Asia/Jakarta')->format('H.i'),
                    'deskripsi' => trim($deskripsiKeuangan)
                ]
            );
        }

        return redirect()->route('keuangan.uangsakus.index')->with('success', 'Status Uang Saku berhasil diupdate!');
    }

    public function destroy(string $id): RedirectResponse
    {
        $uangsaku = UangSaku::findOrFail($id);
        $uangsaku->delete();

        return redirect()->route('keuangan.uangsakus.index')->with('success', 'Uang Saku berhasil dihapus!');
    }
}

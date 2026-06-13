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
        $query = UangSaku::with(['siswa.kelas.tingkat', 'kelas.tingkat']);
        
        $anakSiswas = collect();
        $selectedAnakId = null;

        if (auth()->user()->hasRole('SISWA')) {
            $query->where('siswa_id', auth()->user()->ref_id);
        } elseif (auth()->user()->hasRole('WALI_MURID')) {
            $wali = \Modules\WaliMurid\Models\WaliMurid::with('siswa')->find(auth()->user()->ref_id);
            if ($wali) {
                $anakSiswas = $wali->siswa;
            }
            
            $selectedAnakId = $request->get('santri_id');
            if (!$selectedAnakId && $anakSiswas->count() > 0) {
                $selectedAnakId = $anakSiswas->first()->id;
            }

            if ($selectedAnakId) {
                $query->where('siswa_id', $selectedAnakId);
            }
        }

        $uangsakus = $query->get();
        
        $siswas = collect();
        if (!auth()->user()->hasRole('SISWA') && !auth()->user()->hasRole('WALI_MURID')) {
            $siswas = Siswa::where('siswa.status', 'aktif')
                ->leftJoin('kelas', 'siswa.kelas_id', '=', 'kelas.id')
                ->leftJoin('tingkat', 'kelas.tingkat_id', '=', 'tingkat.id')
                ->select('siswa.*')
                ->orderByRaw("
                    CASE 
                        WHEN tingkat.kode_tingkat = '10' OR tingkat.kode_tingkat = 'X' THEN 10
                        WHEN tingkat.kode_tingkat = '11' OR tingkat.kode_tingkat = 'XI' THEN 11
                        WHEN tingkat.kode_tingkat = '12' OR tingkat.kode_tingkat = 'XII' THEN 12
                        ELSE 99 
                    END ASC
                ")
                ->orderBy('kelas.nama_kelas', 'asc')
                ->orderBy('siswa.nama', 'asc')
                ->with('kelas.tingkat')
                ->get();
        }

        return view('keuangan::uangsakus.index', compact('uangsakus', 'siswas', 'anakSiswas', 'selectedAnakId'));
    }

    public function create(): View
    {
        $siswas = Siswa::where('siswa.status', 'aktif')
            ->leftJoin('kelas', 'siswa.kelas_id', '=', 'kelas.id')
            ->leftJoin('tingkat', 'kelas.tingkat_id', '=', 'tingkat.id')
            ->select('siswa.*')
            ->orderByRaw("
                CASE 
                    WHEN tingkat.kode_tingkat = '10' OR tingkat.kode_tingkat = 'X' THEN 10
                    WHEN tingkat.kode_tingkat = '11' OR tingkat.kode_tingkat = 'XI' THEN 11
                    WHEN tingkat.kode_tingkat = '12' OR tingkat.kode_tingkat = 'XII' THEN 12
                    ELSE 99 
                END ASC
            ")
            ->orderBy('kelas.nama_kelas', 'asc')
            ->orderBy('siswa.nama', 'asc')
            ->with('kelas.tingkat')
            ->get();
        return view('keuangan::uangsakus.create', compact('siswas'));
    }

    public function store(Request $request): RedirectResponse
    {
        $dateRules = 'required|date|before_or_equal:' . date('Y-m-d');
        if (!auth()->check() || !auth()->user()->isSuperAdmin()) {
            $dateRules .= '|after_or_equal:' . date('Y-m-01');
        }

        $request->validate([
            'siswa_id'  => 'required|exists:siswa,id',
            'jumlah'    => 'required|numeric|min:0',
            'tanggal'   => $dateRules,
            'status'    => 'required|string',
            'deskripsi' => 'nullable|string'
        ], [
            'tanggal.after_or_equal' => 'Tanggal tidak boleh kurang dari awal bulan ini.',
            'tanggal.before_or_equal' => 'Tanggal tidak boleh melebihi hari ini.'
        ]);

        $siswa = Siswa::with('kelas.tingkat')->find($request->siswa_id);
        
        // No saldo check for Uang Saku anymore

        $data = $request->all();
        $data['kelas_id'] = $siswa->kelas_id ?? null;

        $uangsaku = UangSaku::create($data);

        $namaSiswa = $siswa->nama ?? 'Unknown';
        $tingkatSantri = isset($siswa->kelas->tingkat) ? "(" . $siswa->kelas->tingkat->nama_tingkat . ")" : "";
        $keterangan = $uangsaku->deskripsi ?: '-';
        $deskripsiKeuangan = "Uang Saku " . $namaSiswa . " " . $tingkatSantri . "\nKeterangan: " . $keterangan;

        // Removed automatic syncing to Pemasukan/Pengeluaran

        return redirect()->route('keuangan.uangsakus.index')->with('success', 'Uang Saku berhasil ditambahkan!');
    }

    public function show(string $id): View
    {
        $uangsaku = UangSaku::with(['siswa.kelas.tingkat', 'kelas.tingkat'])->findOrFail($id);
        if (auth()->user()->hasRole('SISWA') && $uangsaku->siswa_id !== auth()->user()->ref_id) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki wewenang untuk melihat data ini.');
        }
        return view('keuangan::uangsakus.show', compact('uangsaku'));
    }

    public function edit(string $id): View
    {
        if (!auth()->check() || !auth()->user()->isSuperAdmin()) {
            abort(403, 'Akses Ditolak: Hanya super admin yang memiliki wewenang untuk mengubah atau menghapus data uang saku.');
        }
        $uangsaku = UangSaku::findOrFail($id);
        $siswas = Siswa::where('siswa.status', 'aktif')
            ->leftJoin('kelas', 'siswa.kelas_id', '=', 'kelas.id')
            ->leftJoin('tingkat', 'kelas.tingkat_id', '=', 'tingkat.id')
            ->select('siswa.*')
            ->orderByRaw("
                CASE 
                    WHEN tingkat.kode_tingkat = '10' OR tingkat.kode_tingkat = 'X' THEN 10
                    WHEN tingkat.kode_tingkat = '11' OR tingkat.kode_tingkat = 'XI' THEN 11
                    WHEN tingkat.kode_tingkat = '12' OR tingkat.kode_tingkat = 'XII' THEN 12
                    ELSE 99 
                END ASC
            ")
            ->orderBy('kelas.nama_kelas', 'asc')
            ->orderBy('siswa.nama', 'asc')
            ->with('kelas.tingkat')
            ->get();
        return view('keuangan::uangsakus.edit', compact('uangsaku', 'siswas'));
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        if (!auth()->check() || !auth()->user()->isSuperAdmin()) {
            abort(403, 'Akses Ditolak: Hanya super admin yang memiliki wewenang untuk mengubah atau menghapus data uang saku.');
        }

        $dateRules = 'required|date|before_or_equal:' . date('Y-m-d');
        if (!auth()->check() || !auth()->user()->isSuperAdmin()) {
            $dateRules .= '|after_or_equal:' . date('Y-m-01');
        }

        $request->validate([
            'siswa_id'  => 'required|exists:siswa,id',
            'jumlah'    => 'required|numeric|min:0',
            'tanggal'   => $dateRules,
            'status'    => 'required|string',
            'deskripsi' => 'nullable|string'
        ], [
            'tanggal.after_or_equal' => 'Tanggal tidak boleh kurang dari awal bulan ini.',
            'tanggal.before_or_equal' => 'Tanggal tidak boleh melebihi hari ini.'
        ]);

        $uangsaku = UangSaku::findOrFail($id);

        // No saldo check for Uang Saku anymore

        $siswa = Siswa::with('kelas.tingkat')->find($request->siswa_id);
        $data = $request->all();
        $data['kelas_id'] = $siswa->kelas_id ?? null;

        $uangsaku->update($data);

        $namaSiswa = $siswa->nama ?? 'Unknown';
        $tingkatSantri = isset($siswa->kelas->tingkat) ? "(" . $siswa->kelas->tingkat->nama_tingkat . ")" : "";
        $keterangan = $uangsaku->deskripsi ?: '-';
        $deskripsiKeuangan = "Uang Saku " . $namaSiswa . " " . $tingkatSantri . "\nKeterangan: " . $keterangan;

        // Removed automatic syncing to Pemasukan/Pengeluaran

        return redirect()->route('keuangan.uangsakus.index')->with('success', 'Uang Saku berhasil diperbarui!');
    }

    public function updateStatus(Request $request, string $id): RedirectResponse
    {
        $request->validate([
            'status' => 'required|string|in:Belum Diterima Santri,Sudah Diterima Santri'
        ]);

        $uangsaku = UangSaku::findOrFail($id);
        
        // No saldo check for Uang Saku anymore

        $uangsaku->update(['status' => $request->status]);

        $siswa = Siswa::with('kelas.tingkat')->find($uangsaku->siswa_id);
        $namaSiswa = $siswa->nama ?? 'Unknown';
        $tingkatSantri = isset($siswa->kelas->tingkat) ? "(" . $siswa->kelas->tingkat->nama_tingkat . ")" : "";
        $keterangan = $uangsaku->deskripsi ?: '-';
        $deskripsiKeuangan = "Uang Saku " . $namaSiswa . " " . $tingkatSantri . "\nKeterangan: " . $keterangan;

        // Removed automatic syncing to Pemasukan/Pengeluaran

        return redirect()->route('keuangan.uangsakus.index')->with('success', 'Status Uang Saku berhasil diupdate!');
    }

    public function destroy(string $id): RedirectResponse
    {
        if (!auth()->check() || !auth()->user()->isSuperAdmin()) {
            abort(403, 'Akses Ditolak: Hanya super admin yang memiliki wewenang untuk mengubah atau menghapus data uang saku.');
        }
        $uangsaku = UangSaku::findOrFail($id);
        $uangsaku->delete();

        return redirect()->route('keuangan.uangsakus.index')->with('success', 'Uang Saku berhasil dihapus!');
    }
}

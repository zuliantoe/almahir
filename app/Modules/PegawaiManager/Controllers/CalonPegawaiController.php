<?php

namespace Modules\PegawaiManager\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\PegawaiManager\Models\CalonPegawai;
use Modules\PegawaiManager\Models\Pegawai;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CalonPegawaiController extends Controller
{
    public function index(Request $request)
    {
        $query = CalonPegawai::with('typePegawai')->latest();

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $calonPegawai = $query->paginate(10)->withQueryString();

        return view('pegawaimanager::calon-pegawai.index', [
            'title' => 'Daftar Calon Pegawai',
            'breadcrumb' => 'Kepegawaian / Calon Pegawai',
            'calonPegawai' => $calonPegawai
        ]);
    }

    public function create()
    {
        $types = \Modules\PegawaiManager\Models\TypePegawai::all();
        return view('pegawaimanager::calon-pegawai.create', [
            'title' => 'Tambah Pelamar Baru',
            'breadcrumb' => 'Kepegawaian / Calon Pegawai / Tambah',
            'types' => $types
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:calon_pegawai,email',
            'no_hp' => 'nullable|string|max:20',
            'type_pegawai_id' => 'required|uuid',
            'tempat_lahir' => 'nullable|string',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:L,P',
            'alamat' => 'nullable|string',
            'status_seleksi' => 'required|in:baru,wawancara,diterima,ditolak',
            'tanggal_melamar' => 'required|date',
        ]);

        CalonPegawai::create($validated);
        return redirect()->route('pegawaimanager.calon-pegawai.index')->with('success', 'Data pelamar berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $calon = CalonPegawai::findOrFail($id);
        $types = \Modules\PegawaiManager\Models\TypePegawai::all();
        return view('pegawaimanager::calon-pegawai.edit', [
            'title' => 'Edit Data Pelamar',
            'breadcrumb' => 'Kepegawaian / Calon Pegawai / Edit',
            'calon' => $calon,
            'types' => $types
        ]);
    }

    /**
     * Set status calon pegawai menjadi diterima dan konversi ke Pegawai asli
     */
    public function update(Request $request, $id)
    {
        $calon = CalonPegawai::findOrFail($id);

        // Jika request berasal dari tombol status di index
        if ($request->has('action_type') && $request->action_type === 'status_update') {
            if ($request->status_seleksi === 'diterima') {
            try {
                DB::beginTransaction();

                // Analisis tipe posisi untuk penentuan Role & Sinkronisasi
                $calon->load('typePegawai');
                $namaPosisi = strtolower($calon->typePegawai->nama_type ?? '');
                $isGuru = strpos($namaPosisi, 'guru') !== false;
                $isAdmin = strpos($namaPosisi, 'admin') !== false || strpos($namaPosisi, 'staf') !== false || strpos($namaPosisi, 'tu') !== false;

                // 1. Buat Akun User
                $user = User::create([
                    'id' => (string) Str::uuid(),
                    'name' => $calon->nama,
                    'email' => $calon->email,
                    'phone' => $calon->no_hp,
                    'password' => Hash::make('password123'),
                    'account_status' => 'active',
                ]);
                
                // Tentukan Role di System (RBAC Spatie)
                if ($isGuru) {
                    $user->assignRole('GURU');
                } elseif ($isAdmin) {
                    $user->assignRole('STAFF');
                } else {
                    $user->assignRole('PEGAWAI'); // Default role
                }

                // 2. Buat Data Pegawai (Master Data Almahira)
                Pegawai::create([
                    'nama' => $calon->nama,
                    'user_id' => $user->id,
                    'type_pegawai_id' => $calon->type_pegawai_id,
                    'tempat_lahir' => $calon->tempat_lahir,
                    'tanggal_lahir' => $calon->tanggal_lahir,
                    'jenis_kelamin' => $calon->jenis_kelamin,
                    'alamat' => $calon->alamat,
                    'tanggal_masuk' => date('Y-m-d'),
                    'status' => 'aktif',
                    'sisa_cuti' => 12
                ]);

                // 3. Sinkronisasi Data ke Tabel Guru (Khusus untuk sistem eksternal/teman)
                if ($isGuru) {
                    \Modules\Guru\Models\Guru::create([
                        'user_id' => $user->id,
                        'type_pegawai_id' => $calon->type_pegawai_id,
                        'nip' => null, // Dikosongkan agar bisa diisi manual oleh HRD nanti
                        'nama' => $calon->nama,
                        'tempat_lahir' => $calon->tempat_lahir,
                        'tanggal_lahir' => $calon->tanggal_lahir,
                        'jenis_kelamin' => $calon->jenis_kelamin,
                        'alamat' => $calon->alamat,
                        'tanggal_masuk' => date('Y-m-d'),
                        'status' => 'aktif',
                        'sisa_cuti' => 12
                    ]);
                }

                // 4. Update status pelamar dan hilangkan dari daftar (Soft Delete)
                $calon->update(['status_seleksi' => 'diterima']);
                $calon->delete();

                DB::commit();
                return redirect()->route('pegawaimanager.calon-pegawai.index')->with('success', 'Calon berhasil diterima dan telah resmi menjadi Pegawai.');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Terjadi kesalahan saat menerima pegawai: ' . $e->getMessage());
            }
            } else {
                $calon->update(['status_seleksi' => $request->status_seleksi]);
                return redirect()->route('pegawaimanager.calon-pegawai.index')->with('success', 'Status seleksi berhasil diperbarui.');
            }
        }

        // Jika request berasal dari form edit penuh
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:calon_pegawai,email,' . $id,
            'no_hp' => 'nullable|string|max:20',
            'type_pegawai_id' => 'required|uuid',
            'tempat_lahir' => 'nullable|string',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:L,P',
            'alamat' => 'nullable|string',
            'status_seleksi' => 'required|in:baru,wawancara,diterima,ditolak',
            'tanggal_melamar' => 'required|date',
        ]);

        $calon->update($validated);
        return redirect()->route('pegawaimanager.calon-pegawai.index')->with('success', 'Data pelamar berhasil diperbarui.');
    }
    
    public function destroy($id)
    {
        $calon = CalonPegawai::findOrFail($id);
        $calon->delete();
        return redirect()->route('pegawaimanager.calon-pegawai.index')->with('success', 'Data calon pegawai berhasil dihapus.');
    }
}

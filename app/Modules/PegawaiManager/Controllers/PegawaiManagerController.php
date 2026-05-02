<?php

namespace Modules\PegawaiManager\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Models\User;
use Modules\PegawaiManager\Models\Pegawai;
use Modules\PegawaiManager\Models\TypePegawai;
use Illuminate\Validation\Rule;

use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\PegawaiManager\Requests\StorePegawaiRequest;
use Modules\PegawaiManager\Requests\UpdatePegawaiRequest;
use Illuminate\Support\Facades\Cache;

class PegawaiManagerController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Pegawai::with(['user', 'typePegawai'])->latest();

        // Filter: Search Name or Email
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('nama', 'like', '%' . $searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $searchTerm . '%');
            });
        }

        // Filter: Employee Type
        if ($request->has('type') && $request->type != '') {
            $query->where('type_pegawai_id', $request->type);
        }

        // Filter: System Role
        if ($request->has('role') && $request->role != '') {
            $roleName = $request->role;
            $query->whereHas('user', function($q) use ($roleName) {
                $q->role($roleName);
            });
        }

        $pegawaiManagers = $query->paginate(10)->withQueryString();
        
        $types = Cache::remember('all_type_pegawai', 86400, function() {
            return TypePegawai::all();
        });
        $roles = Cache::remember('all_roles', 86400, function() {
            return Role::all();
        });

        return view('pegawaimanager::index', [
            'title' => 'Daftar Pegawai',
            'pegawaiManagers' => $pegawaiManagers,
            'types' => $types,
            'roles' => $roles,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $types = Cache::remember('all_type_pegawai', 86400, function() {
            return TypePegawai::all();
        });
        $roles = Cache::remember('all_roles', 86400, function() {
            return Role::all();
        });

        return view('pegawaimanager::create', [
            'title' => 'Tambah Pegawai',
            'types' => $types,
            'roles' => $roles,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePegawaiRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            // 1. Create User Account
            $user = User::create([
                'id' => (string) Str::uuid(),
                'name' => $validated['nama'],
                'email' => $validated['email'],
                'password' => Hash::make('password123'),
                'account_status' => 'active',
            ]);

            // 2. Assign Selected Role
            $user->assignRole($validated['role_name']);

            // 3. Create Pegawai Instance
            Pegawai::create([
                'nama' => $validated['nama'],
                'user_id' => $user->id,
                'type_pegawai_id' => $validated['type_pegawai_id'],
                'email' => $validated['email'],
                'no_hp' => $validated['no_hp'] ?? null,
                'alamat' => $validated['alamat'] ?? null,
                'tanggal_masuk' => $validated['tanggal_masuk'] ?? null,
            ]);

            DB::commit();

            return redirect()->route('pegawaimanager.index')
                ->with('success', 'Pegawai dan akun user berhasil ditambahkan. Password login default: password123');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

    public function show(string $id): View
    {
        $pegawai = Pegawai::with(['user', 'typePegawai'])->findOrFail($id);

        $currentMonth = now()->month;
        $currentYear = now()->year;

        $absensiStats = [
            'hadir' => $pegawai->absensis()
                ->whereMonth('tanggal', $currentMonth)
                ->whereYear('tanggal', $currentYear)
                ->where('status', 'HADIR')
                ->count(),
            'terlambat' => $pegawai->absensis()
                ->whereMonth('tanggal', $currentMonth)
                ->whereYear('tanggal', $currentYear)
                ->where('status', 'TERLAMBAT')
                ->count(),
        ];

        return view('pegawaimanager::show', [
            'title' => 'Detail Profil Pegawai',
            'pegawai' => $pegawai,
            'absensiStats' => $absensiStats,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): View
    {
        $pegawaiManager = Pegawai::findOrFail($id);
        $types = Cache::remember('all_type_pegawai', 86400, function() {
            return TypePegawai::all();
        });
        $roles = Cache::remember('all_roles', 86400, function() {
            return Role::all();
        });

        return view('pegawaimanager::edit', [
            'title' => 'Edit Pegawai',
            'pegawaiManager' => $pegawaiManager,
            'types' => $types,
            'roles' => $roles
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePegawaiRequest $request, string $id): RedirectResponse
    {
        $pegawai = Pegawai::findOrFail($id);
        
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            // 1. Update Pegawai Record
            $pegawai->update([
                'nama' => $validated['nama'],
                'email' => $validated['email'],
                'type_pegawai_id' => $validated['type_pegawai_id'],
                'no_hp' => $validated['no_hp'],
                'alamat' => $validated['alamat'],
                'tanggal_masuk' => $validated['tanggal_masuk'],
            ]);

            // 2. Synchronize with User Record
            $user = $pegawai->user;
            if ($user) {
                $user->update([
                    'name' => $validated['nama'],
                    'email' => $validated['email'],
                ]);

                // 3. Update User Role
                $user->syncRoles([$validated['role_name']]);
            }

            DB::commit();

            return redirect()->route('pegawaimanager.index')
                ->with('success', 'Data pegawai dan akun user berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy(string $id): RedirectResponse
    {
        $pegawai = Pegawai::with('user')->findOrFail($id);

        try {
            DB::beginTransaction();

            $user = $pegawai->user;

            // Hapus data pegawai
            $pegawai->delete();

            // Hapus akun user terkait (soft delete)
            if ($user) {
                $user->delete();
            }

            DB::commit();

            return redirect()->route('pegawaimanager.index')
                ->with('success', 'Data pegawai dan akun sistem terkait berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('pegawaimanager.index')
                ->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }

    /**
     * Export Pegawai data to CSV (Excel compatible)
     */
    public function export()
    {
        $filename = "laporan_pegawai_" . date('Y-m-d_H-i-s') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = [
            'No', 
            'Nama Lengkap', 
            'Tipe Pegawai', 
            'Role Akses', 
            'No HP', 
            'Email', 
            'Alamat', 
            'Tanggal Masuk'
        ];

        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for Excel UTF-8 compatibility
            fputs($file, "\xEF\xBB\xBF");
            
            fputcsv($file, $columns, ';'); // Use semicolon for Excel intl compatibility

            $no = 1;
            
            // Menggunakan chunk yang dilimit 200 data per loop agar RAM terhindar dari Memory OOM
            Pegawai::with(['user', 'typePegawai'])
                ->orderBy('created_at', 'desc')
                ->chunk(200, function ($pegawais) use ($file, &$no) {
                    foreach ($pegawais as $p) {
                        $role = $p->user ? collect($p->user->roles)->pluck('name')->join(', ') : '-';
                        $row = [
                            $no++,
                            $p->nama,
                            $p->typePegawai->nama_type ?? '-',
                            $role,
                            $p->no_hp ?? '-',
                            $p->email ?? '-',
                            $p->alamat ?? '-',
                            $p->tanggal_masuk ? $p->tanggal_masuk->format('Y-m-d') : '-'
                        ];
                        fputcsv($file, $row, ';');
                    }
                });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show the form for importing data.
     */
    public function importForm(): View
    {
        return view('pegawaimanager::import', [
            'title' => 'Import Data Pegawai'
        ]);
    }

    /**
     * Process the imported CSV file.
     */
    public function processImport(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getPathname(), "r");
        
        $header = true;
        $successCount = 0;
        $errorCount = 0;
        
        try {
            DB::beginTransaction();
            
            // Menggunakan ';' sebagai pemisah karena format Export kita juga menggunakan ';'
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                // Handle jika pemisah ternyata ','
                if (count($data) == 1 && strpos($data[0], ',') !== false) {
                    $data = explode(',', $data[0]);
                }

                if ($header) {
                    $header = false;
                    continue; // Skip baris pertama (header)
                }
                
                // Minimal indeks [1] (Nama Lengkap) dan [5] (Email) ada datanya, sesuai format export
                if (count($data) < 6 || empty($data[1]) || empty($data[5])) {
                    $errorCount++;
                    continue;
                }
                
                $nama = trim($data[1]);
                $email = trim($data[5]);
                
                // Cek tipe pegawai berdasarkan nama tipe, jika tidak ketemu biarkan null atau default
                $nama_type = trim($data[2]);
                $type_id = null;
                if (!empty($nama_type) && $nama_type !== '-') {
                    $type = TypePegawai::where('nama_type', 'like', "%{$nama_type}%")->first();
                    $type_id = $type ? $type->id : null;
                }

                $role_name = trim($data[3]);
                if (empty($role_name) || $role_name === '-') $role_name = 'PEGAWAI';

                $no_hp = trim($data[4]) !== '-' ? trim($data[4]) : null;
                $alamat = trim($data[6]) !== '-' ? trim($data[6]) : null;
                $tanggal = trim($data[7]) !== '-' ? trim($data[7]) : null;
                
                // Cek duplikasi email di User
                if (User::where('email', $email)->exists()) {
                    $errorCount++;
                    continue;
                }

                // 1. Create User
                $user = User::create([
                    'id' => (string) Str::uuid(),
                    'name' => $nama,
                    'email' => $email,
                    'password' => Hash::make('password123'),
                    'account_status' => 'active',
                ]);

                // 2. Assign Role
                if (\App\Models\Role::where('name', $role_name)->exists()) {
                    $user->assignRole($role_name);
                } else {
                    $user->assignRole('PEGAWAI'); // Fallback role
                }

                // 3. Create Pegawai
                Pegawai::create([
                    'nama' => $nama,
                    'user_id' => $user->id,
                    'type_pegawai_id' => $type_id,
                    'email' => $email,
                    'no_hp' => $no_hp,
                    'alamat' => $alamat,
                    'tanggal_masuk' => $tanggal,
                ]);
                
                $successCount++;
            }
            
            fclose($handle);
            DB::commit();
            
            $msg = "Berhasil mengimpor {$successCount} data pegawai baru.";
            if ($errorCount > 0) {
                $msg .= " ({$errorCount} baris dilewati karena format tidak valid atau email sudah terdaftar).";
            }

            if ($successCount > 0) {
                return redirect()->route('pegawaimanager.index')->with('success', $msg);
            } else {
                return redirect()->back()->with('error', 'Gagal mengimpor data. Pastikan format CSV sesuai standar Export dan email belum terdaftar. ' . ($errorCount > 0 ? "({$errorCount} error ditemukan)" : ""));
            }

        } catch (\Exception $e) {
            DB::rollBack();
            if (is_resource($handle)) {
                fclose($handle);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem saat memproses file: ' . $e->getMessage());
        }
    }
}

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
use Carbon\Carbon;

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
                  ->orWhereHas('user', function($userQuery) use ($searchTerm) {
                      $userQuery->where('email', 'like', '%' . $searchTerm . '%');
                  });
            });
        }

        // Filter: Employee Type
        if ($request->has('type') && $request->type != '') {
            $query->where('type_pegawai_id', $request->type);
        }

        // Filter: System Role — using scopeWithRole() defined in User model
        if ($request->has('role') && $request->role != '') {
            $roleName = $request->role;
            $query->whereHas('user', function($q) use ($roleName) {
                $q->withRole($roleName);
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

    public function create(Request $request)
    {
        if (!$request->has('manual')) {
            $wawancara = \Modules\PegawaiManager\Models\CalonPegawai::with('typePegawai')
                ->where('status_seleksi', 'wawancara')
                ->get();
                
            return view('pegawaimanager::create-selection', [
                'title' => 'Pilih Sumber Pegawai Baru',
                'wawancara' => $wawancara
            ]);
        }

        $types = Cache::remember('all_type_pegawai', 86400, function() {
            return TypePegawai::all();
        });
        $roles = Cache::remember('all_roles', 86400, function() {
            return Role::all();
        });

        return view('pegawaimanager::create', [
            'title' => 'Tambah Pegawai Manual',
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
            $password = Str::random(10);
            $user = User::create([
                'id' => (string) Str::uuid(),
                'name' => $validated['nama'],
                'email' => $validated['email'],
                'phone' => $validated['no_hp'] ?? null,
                'password' => Hash::make($password),
                'account_status' => 'active',
                'must_change_password' => true,
            ]);

            // 2. Assign Selected Role
            $user->assignRole($validated['role_name']);

            // 3. Create Pegawai Instance
            Pegawai::create([
                'nama' => $validated['nama'],
                'user_id' => $user->id,
                'type_pegawai_id' => $validated['type_pegawai_id'],
                'alamat' => $validated['alamat'] ?? null,
                'tanggal_masuk' => $validated['tanggal_masuk'] ?? null,
            ]);

            DB::commit();

            return redirect()->route('pegawaimanager.index')
                ->with('success', 'Pegawai dan akun user berhasil ditambahkan.')
                ->with('new_password', $password);

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
                ->whereIn('status', ['TEPAT WAKTU', 'TERLAMBAT'])
                ->count(),
            'terlambat' => $pegawai->absensis()
                ->whereMonth('tanggal', $currentMonth)
                ->whereYear('tanggal', $currentYear)
                ->where('status', 'TERLAMBAT')
                ->count(),
            'izin' => $pegawai->perizinans()
                ->where('status', 'disetujui')
                ->where(function($q) use ($currentMonth, $currentYear) {
                    $q->where(function($q2) use ($currentMonth, $currentYear) {
                        $q2->whereMonth('tanggal_mulai', $currentMonth)
                           ->whereYear('tanggal_mulai', $currentYear);
                    })->orWhere(function($q2) use ($currentMonth, $currentYear) {
                        $q2->whereMonth('tanggal_selesai', $currentMonth)
                           ->whereYear('tanggal_selesai', $currentYear);
                    });
                })
                ->count(), // Ubah menjadi count (Total Perizinan)
        ];

        $izinStats = [
            'total'     => $pegawai->perizinans()->whereYear('created_at', $currentYear)->get()->sum(function($i) {
                return Carbon::parse($i->tanggal_mulai)->diffInDays(Carbon::parse($i->tanggal_selesai)) + 1;
            }),
            'disetujui' => $pegawai->perizinans()->whereYear('created_at', $currentYear)->where('status', 'disetujui')->get()->sum(function($i) {
                return Carbon::parse($i->tanggal_mulai)->diffInDays(Carbon::parse($i->tanggal_selesai)) + 1;
            }),
            'menunggu'  => $pegawai->perizinans()->whereYear('created_at', $currentYear)->where('status', 'menunggu')->get()->sum(function($i) {
                return Carbon::parse($i->tanggal_mulai)->diffInDays(Carbon::parse($i->tanggal_selesai)) + 1;
            }),
            'ditolak'   => $pegawai->perizinans()->whereYear('created_at', $currentYear)->where('status', 'ditolak')->get()->sum(function($i) {
                return Carbon::parse($i->tanggal_mulai)->diffInDays(Carbon::parse($i->tanggal_selesai)) + 1;
            }),
            'sisa_cuti' => $pegawai->getAvailableQuota(),
        ];

        return view('pegawaimanager::show', [
            'title'        => 'Detail Profil Pegawai',
            'pegawai'      => $pegawai,
            'absensiStats' => $absensiStats,
            'izinStats'    => $izinStats,
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
                'type_pegawai_id' => $validated['type_pegawai_id'],
                'alamat' => $validated['alamat'],
                'tanggal_masuk' => $validated['tanggal_masuk'],
            ]);

            // 2. Synchronize with User Record
            $user = $pegawai->user;
            if ($user) {
                $user->update([
                    'name' => $validated['nama'],
                    'email' => $validated['email'],
                    'phone' => $validated['no_hp'],
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

    public function toggleStatus(string $id): RedirectResponse
    {
        $pegawai = Pegawai::with('user')->findOrFail($id);
        $user = $pegawai->user;

        if (!$user) {
            return redirect()->back()->with('error', 'Akun user tidak ditemukan untuk pegawai ini.');
        }

        try {
            $newStatus = $user->account_status === 'active' ? 'inactive' : 'active';
            $user->update(['account_status' => $newStatus]);

            $statusText = $newStatus === 'active' ? 'diaktifkan' : 'dinonaktifkan';
            
            return redirect()->back()->with('success', "Akun pegawai {$pegawai->nama} berhasil {$statusText}.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
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
            'NIP',
            'Nama Lengkap', 
            'Tipe Pegawai', 
            'Email',
            'No HP', 
            'Role Akses', 
            'Tempat Lahir',
            'Tanggal Lahir',
            'Jenis Kelamin',
            'Alamat', 
            'Tanggal Masuk',
            'Status'
        ];

        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for Excel UTF-8 compatibility
            fputs($file, "\xEF\xBB\xBF");
            
            fputcsv($file, $columns, ';'); // Use semicolon for Excel intl compatibility

            $no = 1;
            
            Pegawai::with(['user', 'typePegawai'])
                ->orderBy('created_at', 'desc')
                ->chunk(200, function ($pegawais) use ($file, &$no) {
                    foreach ($pegawais as $p) {
                        $role = $p->user ? collect($p->user->roles)->pluck('name')->join(', ') : '-';
                        $row = [
                            $no++,
                            $p->nip ?? '-',
                            $p->nama,
                            $p->typePegawai->nama_type ?? '-',
                            $p->user->email ?? '-',
                            $p->user->phone ?? '-',
                            $role,
                            $p->tempat_lahir ?? '-',
                            $p->tanggal_lahir ?? '-',
                            $p->jenis_kelamin ?? '-',
                            $p->alamat ?? '-',
                            $p->tanggal_masuk ? $p->tanggal_masuk->format('Y-m-d') : '-',
                            $p->status ?? '-'
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
                
                // Validasi minimal (Nama [2] dan Email [4] wajib ada)
                if (count($data) < 5 || empty(trim($data[2])) || empty(trim($data[4]))) {
                    $errorCount++;
                    continue;
                }
                
                $nip = trim($data[1]) !== '-' ? trim($data[1]) : null;
                $nama = trim($data[2]);
                $email = trim($data[4]);
                
                // Cek tipe pegawai berdasarkan nama tipe
                $nama_type = trim($data[3]);
                $type_id = null;
                if (!empty($nama_type) && $nama_type !== '-') {
                    $type = TypePegawai::where('nama_type', 'like', "%{$nama_type}%")->first();
                    $type_id = $type ? $type->id : null;
                }

                $role_name = trim($data[6]);
                if (empty($role_name) || $role_name === '-') $role_name = 'PEGAWAI';

                $no_hp = trim($data[5]) !== '-' ? trim($data[5]) : null;
                $tempat_lahir = isset($data[7]) && trim($data[7]) !== '-' ? trim($data[7]) : null;
                $tanggal_lahir = isset($data[8]) && trim($data[8]) !== '-' ? trim($data[8]) : null;
                $jenis_kelamin = isset($data[9]) && trim($data[9]) !== '-' ? trim($data[9]) : null;
                $alamat = isset($data[10]) && trim($data[10]) !== '-' ? trim($data[10]) : null;
                $tanggal = isset($data[11]) && trim($data[11]) !== '-' ? trim($data[11]) : null;
                $status = isset($data[12]) && trim($data[12]) !== '-' ? trim(strtolower($data[12])) : 'aktif';
                
                // Cek duplikasi email di User
                if (User::where('email', $email)->exists()) {
                    $errorCount++;
                    continue;
                }

                // Cek duplikasi NIP di Pegawai jika NIP diisi
                if (!empty($nip) && Pegawai::where('nip', $nip)->exists()) {
                    $errorCount++;
                    continue;
                }

                // 1. Create User
                $user = User::create([
                    'id' => (string) Str::uuid(),
                    'name' => $nama,
                    'email' => $email,
                    'phone' => $no_hp,
                    'password' => Hash::make('Almahir@2026!'),
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
                    'nip' => $nip,
                    'tempat_lahir' => $tempat_lahir,
                    'tanggal_lahir' => $tanggal_lahir,
                    'jenis_kelamin' => $jenis_kelamin,
                    'alamat' => $alamat,
                    'tanggal_masuk' => $tanggal,
                    'status' => in_array($status, ['aktif', 'nonaktif', 'pensiun']) ? $status : 'aktif',
                    'sisa_cuti' => 12 // Default
                ]);

                // 4. Sinkronisasi Data ke Tabel Guru jika posisinya Guru
                $isGuru = strpos(strtolower($nama_type), 'guru') !== false;
                if ($isGuru) {
                    \Modules\Guru\Models\Guru::create([
                        'user_id' => $user->id,
                        'type_pegawai_id' => $type_id,
                        'nip' => $nip,
                        'nama' => $nama,
                        'tempat_lahir' => $tempat_lahir,
                        'tanggal_lahir' => $tanggal_lahir,
                        'jenis_kelamin' => $jenis_kelamin,
                        'alamat' => $alamat,
                        'tanggal_masuk' => $tanggal,
                        'status' => in_array($status, ['aktif', 'nonaktif', 'pensiun']) ? $status : 'aktif',
                        'sisa_cuti' => 12 // Default
                    ]);
                }
                
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

    /**
     * Reset Password User
     */
    public function resetPassword(string $id): RedirectResponse
    {
        $pegawai = Pegawai::with('user')->findOrFail($id);
        $user = $pegawai->user;

        if (!$user) {
            return redirect()->back()->with('error', 'Akun user tidak ditemukan untuk pegawai ini.');
        }

        // Batasi maksimal 3 kali reset per user dalam 1 hari
        $today = date('Y-m-d');
        $cacheKey = "password_reset_count:{$user->id}:{$today}";
        $resetCount = Cache::get($cacheKey, 0);

        if ($resetCount >= 3) {
            return redirect()->back()->with('error', 'Gagal. Batas reset password untuk pegawai ini hari ini sudah mencapai maksimal (3 kali).');
        }

        try {
            $newPassword = Str::random(10);
            $user->update([
                'password' => Hash::make($newPassword),
                'must_change_password' => true
            ]);

            // Tambahkan hitungan reset di Cache selama 24 jam
            Cache::put($cacheKey, $resetCount + 1, 86400);

            return redirect()->back()
                ->with('success', 'Password berhasil di-reset.')
                ->with('new_password', $newPassword);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mereset password: ' . $e->getMessage());
        }
    }

    /**
     * Print QR Card for Employee.
     */
    public function printCard($id): View
    {
        $pegawai = Pegawai::with(['user', 'typePegawai'])->findOrFail($id);

        if (!$pegawai->qr_token) {
            $pegawai->update(['qr_token' => (string) \Illuminate\Support\Str::uuid()]);
        }

        return view('pegawaimanager::print-card', [
            'title' => 'Cetak Kartu Pegawai - ' . $pegawai->nama,
            'pegawai' => $pegawai
        ]);
    }
}

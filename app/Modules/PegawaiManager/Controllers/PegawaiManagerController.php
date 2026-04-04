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

class PegawaiManagerController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $pegawaiManagers = Pegawai::with(['user', 'typePegawai'])->latest()->get();

        return view('pegawaimanager::index', [
            'title' => 'Daftar Pegawai',
            'pegawaiManagers' => $pegawaiManagers,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $types = TypePegawai::all();
        $roles = Role::all();

        return view('pegawaimanager::create', [
            'title' => 'Tambah Pegawai',
            'types' => $types,
            'roles' => $roles,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'type_pegawai_id' => 'required|uuid',
            'email' => 'required|email|unique:sys_users,email|unique:pegawai,email',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'tanggal_masuk' => 'nullable|date',
            'role_name' => 'required|string|exists:sys_roles,name',
        ]);

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

    /**
     * Display the specified resource.
     */
    public function show(string $id): View
    {
        $pegawaiManager = Pegawai::findOrFail($id);

        return view('pegawaimanager::show', [
            'title' => 'Detail Pegawai',
            'pegawaiManager' => $pegawaiManager,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): View
    {
        $pegawaiManager = Pegawai::findOrFail($id);
        $types = TypePegawai::all();
        $roles = Role::all();

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
    public function update(Request $request, string $id): RedirectResponse
    {
        $pegawai = Pegawai::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'type_pegawai_id' => 'required|uuid',
            'email' => [
                'required',
                'email',
                Rule::unique('sys_users', 'email')->ignore($pegawai->user_id),
                Rule::unique('pegawai', 'email')->ignore($pegawai->id),
            ],
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'tanggal_masuk' => 'nullable|date',
            'role_name' => 'required|string|exists:sys_roles,name',
        ]);

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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        $pegawai = Pegawai::findOrFail($id);

        $pegawai->delete();

        return redirect()->route('pegawaimanager.index')
            ->with('success', 'Data pegawai berhasil dihapus.');
    }
}

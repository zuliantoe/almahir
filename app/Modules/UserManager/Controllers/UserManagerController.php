<?php

namespace Modules\UserManager\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Modules\Guru\Models\Guru;
use Modules\Siswa\Models\Siswa;
use Modules\WaliMurid\Models\WaliMurid;

/**
 * UserManagerController
 * 
 * CRUD operations for user management (SUPER_ADMIN only)
 * Supports linking users to Siswa/Guru/WaliMurid data
 */
class UserManagerController extends Controller
{
    /**
     * Role to Model mapping for data linking
     */
    protected $roleModelMap = [
        'SISWA' => Siswa::class,
        'GURU' => Guru::class,
        'WALI_MURID' => WaliMurid::class,
    ];

    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $query = User::with('roles');

        if ($request->has('role') && $request->role) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(20);
        $roles = Role::all();

        return view('usermanager::index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        $roles = Role::all();
        return view('usermanager::create', compact('roles'));
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:sys_users,email',
            'password' => 'required|min:8|confirmed',
            'ref_type' => 'nullable|string',
            'ref_id' => 'nullable|uuid',
            'account_status' => 'required|in:active,inactive',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:sys_roles,id',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'ref_type' => $validated['ref_type'] ?? null,
            'ref_id' => $validated['ref_id'] ?? null,
            'account_status' => $validated['account_status'],
        ]);

        // Assign roles
        $user->syncRoles($validated['roles']);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil dibuat.');
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit(string $id)
    {
        $user = User::with('roles')->findOrFail($id);
        $roles = Role::all();

        // Get linked data info
        $linkedData = null;
        if ($user->ref_type && $user->ref_id) {
            $linkedData = $this->getLinkedDataInfo($user);
        }

        return view('usermanager::edit', compact('user', 'roles', 'linkedData'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('sys_users')->ignore($user->id)],
            'password' => 'nullable|min:8|confirmed',
            'ref_type' => 'nullable|string',
            'ref_id' => 'nullable|uuid',
            'account_status' => 'required|in:active,inactive',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:sys_roles,id',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'ref_type' => $validated['ref_type'] ?? null,
            'ref_id' => $validated['ref_id'] ?? null,
            'account_status' => $validated['account_status'],
        ]);

        if ($validated['password']) {
            $user->update([
                'password' => Hash::make($validated['password']),
            ]);
        }

        $user->syncRoles($validated['roles']);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil diupdate.');
    }

    /**
     * Soft delete the specified user
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        if ($user->hasRole('SUPER_ADMIN') && User::whereHas('roles', function ($q) {
            $q->where('name', 'SUPER_ADMIN');
        })->count() <= 1) {
            return back()->with('error', 'Tidak dapat menghapus satu-satunya Super Admin.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    /**
     * Toggle user account status
     */
    public function toggleStatus(string $id)
    {
        $user = User::findOrFail($id);
        $newStatus = $user->account_status === 'active' ? 'inactive' : 'active';
        $user->update(['account_status' => $newStatus]);

        return back()->with('success', "Status user berhasil diubah menjadi {$newStatus}.");
    }

    /**
     * AJAX: Get available data for linking based on role
     */
    public function getLinkableData(Request $request)
    {
        $roleName = $request->get('role');
        
        if (!isset($this->roleModelMap[$roleName])) {
            return response()->json([]);
        }

        $modelClass = $this->roleModelMap[$roleName];
        
        // Get data that doesn't already have a linked user
        $data = $modelClass::whereDoesntHave('user')
            ->select('id', 'nama', $this->getIdentifierField($roleName))
            ->get()
            ->map(function ($item) use ($roleName) {
                $identifier = $this->getIdentifierField($roleName);
                return [
                    'id' => $item->id,
                    'nama' => $item->nama,
                    'identifier' => $item->$identifier ?? null,
                ];
            });

        return response()->json($data);
    }

    /**
     * Get the identifier field for each role type
     */
    protected function getIdentifierField(string $roleName): string
    {
        return match($roleName) {
            'SISWA' => 'nis',
            'GURU' => 'nip',
            'WALI_MURID' => 'telepon',
            default => 'id',
        };
    }

    /**
     * Get ref_type value for model class
     */
    protected function getRefType(string $roleName): string
    {
        return match($roleName) {
            'SISWA' => Siswa::class,
            'GURU' => Guru::class,
            'WALI_MURID' => WaliMurid::class,
            default => '',
        };
    }

    /**
     * Get linked data info for display
     */
    protected function getLinkedDataInfo(User $user): ?array
    {
        if (!$user->ref_type || !$user->ref_id) {
            return null;
        }

        try {
            $model = $user->ref_type::find($user->ref_id);
            if (!$model) return null;

            $type = match($user->ref_type) {
                Siswa::class => 'Siswa',
                Guru::class => 'Guru',
                WaliMurid::class => 'Wali Murid',
                default => 'Unknown',
            };

            return [
                'type' => $type,
                'nama' => $model->nama,
                'model' => $model,
            ];
        } catch (\Exception $e) {
            return null;
        }
    }
}

<?php

namespace Modules\RolePermission\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Services\PermissionRegistry;
use Illuminate\Http\Request;

/**
 * RolePermissionController
 * 
 * CRUD operations for Role & Permission management.
 * Only accessible by SUPER_ADMIN.
 * 
 * @author SIAKAD Development Team
 */
class RolePermissionController extends Controller
{
    /**
     * Display a listing of roles
     */
    public function index()
    {
        $roles = Role::withCount('users')->get();
        
        return view('rolepermission::index', [
            'title' => 'Roles & Permissions',
            'roles' => $roles,
            'totalPermissions' => PermissionRegistry::count(),
        ]);
    }

    /**
     * Show the form for creating a new role
     */
    public function create()
    {
        return view('rolepermission::create', [
            'title' => 'Tambah Role Baru',
            'permissionGroups' => PermissionRegistry::all(),
        ]);
    }

    /**
     * Store a newly created role
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:sys_roles,name|regex:/^[A-Z_]+$/',
            'display_name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
        ], [
            'name.regex' => 'Nama role harus huruf kapital dan underscore saja (contoh: WALI_KELAS)',
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'display_name' => $validated['display_name'],
            'description' => $validated['description'] ?? null,
            'permissions' => $validated['permissions'] ?? [],
            'is_system' => false,
        ]);

        return redirect()->route('rolepermission.index')
            ->with('success', "Role '{$role->display_name}' berhasil dibuat.");
    }

    /**
     * Show the form for editing a role
     */
    public function edit(string $id)
    {
        $role = Role::findOrFail($id);
        
        return view('rolepermission::edit', [
            'title' => 'Edit Role: ' . $role->display_name,
            'role' => $role,
            'permissionGroups' => PermissionRegistry::all(),
        ]);
    }

    /**
     * Update the specified role
     */
    public function update(Request $request, string $id)
    {
        $role = Role::findOrFail($id);
        
        $validated = $request->validate([
            'display_name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
        ]);

        // System roles can only update permissions, not name
        $updateData = [
            'display_name' => $validated['display_name'],
            'description' => $validated['description'] ?? null,
            'permissions' => $validated['permissions'] ?? [],
        ];

        // Non-system roles can update name too
        if (!$role->is_system && $request->has('name')) {
            $request->validate([
                'name' => 'required|string|max:50|unique:sys_roles,name,' . $id . '|regex:/^[A-Z_]+$/',
            ]);
            $updateData['name'] = $request->name;
        }

        $role->update($updateData);

        return redirect()->route('rolepermission.index')
            ->with('success', "Role '{$role->display_name}' berhasil diperbarui.");
    }

    /**
     * Remove the specified role
     */
    public function destroy(string $id)
    {
        $role = Role::findOrFail($id);

        // Prevent deletion of system roles
        if ($role->is_system) {
            return redirect()->route('rolepermission.index')
                ->with('error', 'Role sistem tidak dapat dihapus.');
        }

        // Check if role has users
        if ($role->users()->count() > 0) {
            return redirect()->route('rolepermission.index')
                ->with('error', "Role '{$role->display_name}' tidak dapat dihapus karena masih memiliki user.");
        }

        $roleName = $role->display_name;
        $role->delete();

        return redirect()->route('rolepermission.index')
            ->with('success', "Role '{$roleName}' berhasil dihapus.");
    }
}

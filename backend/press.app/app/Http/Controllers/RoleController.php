<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    private const MANAGED_ROLES = ['Admin', 'Operator', 'Penulis', 'Kepala Penulis'];

    /**
     * Daftar semua roles.
     */
    public function index(Request $request)
    {
        $query = Role::withCount('users')->whereIn('name', self::MANAGED_ROLES);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $perPage = $request->input('per_page', 10);
        $roles = $query->orderBy('id')->paginate($perPage);

        return response()->json($roles);
    }

    /**
     * Buat role baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles', Rule::in(self::MANAGED_ROLES)],
            'description' => 'nullable|string|max:500',
            'status' => 'in:Active,Inactive',
        ]);

        $role = Role::create($request->only(['name', 'description', 'status']));

        return response()->json($role, 201);
    }

    /**
     * Detail satu role.
     */
    public function show(Role $role)
    {
        abort_unless(in_array($role->name, self::MANAGED_ROLES, true), 404);

        $role->loadCount('users');
        return response()->json($role);
    }

    /**
     * Update role.
     */
    public function update(Request $request, Role $role)
    {
        abort_unless(in_array($role->name, self::MANAGED_ROLES, true), 404);

        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,' . $role->id, Rule::in(self::MANAGED_ROLES)],
            'description' => 'nullable|string|max:500',
            'status' => 'in:Active,Inactive',
        ]);

        $role->update($request->only(['name', 'description', 'status']));

        return response()->json($role);
    }

    /**
     * Hapus role.
     */
    public function destroy(Role $role)
    {
        abort_unless(in_array($role->name, self::MANAGED_ROLES, true), 404);

        // Cek apakah masih ada user dengan role ini
        if ($role->users()->count() > 0) {
            return response()->json([
                'message' => 'Tidak dapat menghapus role yang masih memiliki user.',
            ], 422);
        }

        $role->delete();

        return response()->json(['message' => 'Role berhasil dihapus.']);
    }
}

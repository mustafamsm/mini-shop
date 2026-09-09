<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view permissions')->only(['index', 'edit']);
        $this->middleware('permission:create permissions')->only(['create', 'store']);
        $this->middleware('permission:edit permissions')->only(['update']);
        $this->middleware('permission:delete permissions')->only(['destroy']);
    }
    public function index()
    {
         return Inertia::render('Admin/Permissions/Index', [
            'permissions' => Permission::with('roles')->withCount('roles')->get(),
            'roles' => Role::pluck('name'),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:permissions,name',
        ]);

        Permission::create(['name' => $request->name]);

        return redirect()->back()->with('toast', ['type' => 'success', 'message' => 'Permission created successfully.']);
    }
    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|string|unique:permissions,name,' . $permission->id,
        ]);

        $permission->update(['name' => $request->name]);

        return redirect()->back()->with('toast', ['type' => 'success', 'message' => 'Permission updated successfully.']);
    }
    public function destroy(Permission $permission)
    {
        if ($permission->roles()->count() > 0) {
            return redirect()->back()->with('toast', ['type' => 'error', 'message' => 'Cannot delete permission assigned to roles.']);
        }
        $permission->delete();

        return redirect()->back()->with('toast', ['type' => 'success', 'message' => 'Permission deleted successfully.']);
    }
    public function toggleRole(Request $request, Permission $permission)
    {

    $validated = $request->validate([
        'role' => 'required|string|exists:roles,name',
        'enabled' => 'required|boolean',
    ]);
        $role = Role::findByName($validated['role']);
        $validated['enabled'] ? $role->givePermissionTo($permission) : $role->revokePermissionTo($permission);
        return back()->with('toast', ['type' => 'success', 'message' => 'Permission updated successfully.']);
    }
}

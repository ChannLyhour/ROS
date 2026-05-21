<?php

namespace App\Http\Controllers\admin;

use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Role::class);
        $roles = Role::withCount('users')->get();
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $this->authorize('create', Role::class);
        $permissions = Permission::all();
        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Role::class);
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array'
        ]);

        $role = Role::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'guard_name' => 'web'
        ]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('roles.index')->with('success', 'Role created successfully!');
    }

    public function edit(Role $role)
    {
        $this->authorize('update', $role);
        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function show(Role $role)
    {
        $this->authorize('view', $role);
        return $this->edit($role);
    }

    public function update(Request $request, Role $role)
    {
        $this->authorize('update', $role);
        $request->validate([
            'name' => 'required|string|unique:roles,name,' . $role->id,
            'description' => 'nullable|string',
            'permissions' => 'nullable|array'
        ]);

        $role->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description
        ]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        } else {
            $role->syncPermissions([]);
        }

        return redirect()->route('roles.index')->with('success', 'Role updated successfully!');
    }

    public function destroy(Role $role)
    {
        $this->authorize('delete', $role);
        if ($role->users()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete role that has active users!');
        }

        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Role deleted successfully!');
    }
}

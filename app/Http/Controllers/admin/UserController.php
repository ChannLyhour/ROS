<?php

namespace App\Http\Controllers\admin;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Helper\UploadImageHelper;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);
        $query = User::with('role');

        if ($request->filled('search')) {
            $query->where('name', 'LIKE', "%{$request->search}%")
                  ->orWhere('email', 'LIKE', "%{$request->search}%");
        }

        if ($request->filled('role')) {
            $query->where('role_id', $request->role);
        }

        $users = $query->paginate(10);
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $this->authorize('create', User::class);
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', User::class);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role_id' => 'required|exists:roles,id',
            'password' => 'required|string|min:8|confirmed',
            'image_file' => 'nullable|image|max:2048'
        ]);

        $data = $request->except(['password', 'image_file']);
        $data['password'] = Hash::make($request->password);

        if ($request->hasFile('image_file')) {
            $data['image'] = UploadImageHelper::store($request->file('image_file'), 'users');
        }

        $user = User::create($data);

        // Sync Spatie Role
        $role = Role::find($request->role_id);
        if ($role) {
            $user->assignRole($role->name);
        }

        return redirect()->route('users.index')->with('success', 'User created successfully!');
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role_id' => 'required|exists:roles,id',
            'password' => 'nullable|string|min:8|confirmed',
            'image_file' => 'nullable|image|max:2048'
        ]);

        $data = $request->except(['password', 'image_file']);
        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('image_file')) {
            $data['image'] = UploadImageHelper::store($request->file('image_file'), 'users', 'public', $user->image);
        }

        $user->update($data);

        // Sync Spatie Role
        $role = Role::find($request->role_id);
        if ($role) {
            $user->syncRoles([$role->name]);
        }

        return redirect()->route('users.index')->with('success', 'User updated successfully!');
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot delete yourself!');
        }
        
        if ($user->image) UploadImageHelper::delete($user->image);
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully!');
    }
}

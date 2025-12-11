<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    //
    public function index()
    {
        $users = User::all();

        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::all();
        $branches = Branch::all();

        return view('admin.users.create', compact('roles', 'branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:users',
            'email' => 'required|unique:users',
            'role_id' => 'required',
            'branch_id' => 'required',
            'password' => 'required'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'branch_id' => $request->branch_id,
            'password' => Hash::make($request->password),
        ]);
        $role = Role::find($request->role_id);
        $user->assignRole($role);
        return redirect()->route('admin.users.index')->with('success', __('main.created'));
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if ($user) {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);
            $role = Role::find($request->role_id);
            $user->assignRole($role);
            return redirect()->route('admin.users.index')->with('success', __('main.updated'));
        }
    }

    public function show($id)
    {
        $user = User::findorfail($id);
        return view('admin.users.show', compact('user'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        $branches = Branch::all();
        $userRole = $user->roles->pluck('id')->all();

        return view('admin.users.edit', compact('user', 'roles', 'branches', 'userRole'));
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if ($user) {
            $user->delete();
            return redirect()->route('admin.users.index')->with('success', __('main.deleted'));
        }
    }
}

<?php

namespace App\Http\Controllers\Admin;

 
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin; 
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\DB;


class PermissionController extends Controller
{


    public function add()
    {
       return view('admin.roles.add');
    }

    public function create(Request $request)
    {

        $role = Role::findOrFail(1);
   
        $permission=Permission::create([ 'name' => $request->input('name'),'key' => $request->input('key')]);

        $role->givePermissionTo($permission);
      
        $permission->assignRole($role);

        $permission = Permission::get();
        return $permission;


    }
 
    public function show()
    {


        return Auth::user()->permissions;


    }

}

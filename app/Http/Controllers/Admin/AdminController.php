<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; 
use App\Models\Branch;
use App\Models\User; 
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function edit_profile($id)
    {
        $user = User::findOrFail($id);
        return view('admin.profiles.edit', compact('user'));
    }

    public function update_profile(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'same:confirm-password'
        ]);

        $input = $request->all();
        if (!empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            $input = array_except($input, array('password'));
        }

        $user = User::findOrFail($id);
        $user->update($input);
        if ($request->hasFile('profile_pic')) {
            $profile_pic = $request->file('profile_pic');
            $fileName = $profile_pic->getClientOriginalName();
            $uploadDir = 'uploads/profiles/admins/' . $id;
            $profile_pic->move($uploadDir, $fileName);
            $user->profile_pic = $uploadDir . '/' . $fileName;
            $user->save();
        }
        return redirect()->back()->with('success', 'تم تحديث البيانات الشخصية بنجاح ');
    }


    public function index(Request $request)
    {
        $data = User::all();
        $roles = Role::get()->pluck('name', 'name');
        return view('admin.admins.index', compact('data', 'roles'));
    }
	

    public function create()
    {
        $roles = Role::where('guard_name', 'admin-web')
            ->get()->pluck('name', 'name');
        $branches = Branch::all();
        return view('admin.admins.create', compact('roles','branches'));

    }

    public function store(Request $request)
    {

        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|same:confirm-password',
            'role_name' => 'required'
        ]);

        $usersCount = DB::table('users')->count();
        $maxUsers = DB::table('system_settings')->select('max_users')->first()->max_users;

        if($usersCount >= $maxUsers){
            return redirect()->back()->with('error',__('main.Max Users Reached'));
        }

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $Admin = User::create($input);
        $Admin->assignRole($request->input('role_name')); 
		$permissions=$Admin->getPermissionsViaRoles();
		$Admin->givePermissionTo($permissions);

        return redirect()->route('admin.admins.index')
            ->with('success', 'تم اضافة مستخدم بنجاح');
    }

    public function show($id)
    {
        $Admin = User::findorfail($id);
        return view('admin.admins.show', compact('Admin'));
    }


    public function edit($id)
    {
        $admin = User::findOrFail($id);
        $roles = Role::where('guard_name', 'admin-web')
            ->get()->pluck('name', 'name'); 
        $adminsRole = $admin->roles->pluck('name', 'name')->first();
        $branches = Branch::all();
        return view('admin.admins.edit', compact('admin', 'roles','branches', 'adminsRole'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'same:confirm-password',
            'role_name' => 'required'
        ]);
        $input = $request->all();
        if (!empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            $input = array_except($input, array('password'));
        }
        $Admin = User::findOrFail($id);
        $Admin->update($input);
        DB::table('model_has_roles')->where('model_id', $id)->delete();
        $Admin->assignRole($request->input('role_name'));
		
        $roles=$Admin->hasAllRoles(Role::all());
		$permissions=$Admin->getPermissionsViaRoles();
		$Admin->givePermissionTo($permissions);
		
        if ($request->hasFile('profile_pic')) {
            $image = $request->file('profile_pic');
            $fileName = $image->getClientOriginalName();
            $uploadDir = 'uploads/profiles/admins/' . $Admin->id;
            $image->move($uploadDir, $fileName);
            $Admin->profile_pic = $uploadDir . '/' . $fileName;
            $Admin->save();
        }
        return redirect()->route('admin.admins.index')
            ->with('success', 'تم تعديل بيانات المستخدم بنجاح');
		 
    }

    public function destroy(Request $request)
    {
  
        User::findOrFail($request->Admin_id)->delete();
        return redirect()->route('admin.admins.index')
            ->with('success', 'تم حذف المستخدم بنجاح');
 
    }

    public function remove_selected(Request $request)
    {
        /*
        $Admins_id = $request->Admins;
        foreach ($Admins_id as $Admin_id) {
            $Admin = Admin::FindOrFail($Admin_id);
            $Admin->delete();
        }
        return redirect()->route('admin.admins.index')
            ->with('success', 'تم الحذف بنجاح');
            */
    }

    public function print_selected()
    {
        $Admins = User::all();
        return view('admin.admins.print', compact('Admins'));
    }

    public function export_Admins_excel()
    {
        return Excel::download(new AdminsExport(), 'كل المستخدمين.xlsx');
    }
}

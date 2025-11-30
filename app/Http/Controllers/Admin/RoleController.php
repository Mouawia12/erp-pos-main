<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; 
use Illuminate\Http\Request; 
use App\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:عرض صلاحية', ['only' => ['index']]);
        $this->middleware('permission:اضافة صلاحية', ['only' => ['create', 'store']]);
        $this->middleware('permission:تعديل صلاحية', ['only' => ['edit', 'update']]);
        $this->middleware('permission:حذف صلاحية', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $roles = Role::query()
            ->where('guard_name', 'admin-web')
            ->orderBy('id', 'ASC')
            ->get();

        if (Auth::user()->subscriber_id) {
            $roles = $roles->where('name', '!=', 'system_owner');
        }

        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permission = Permission::get();
        if(Auth::user()->subscriber_id){
            $permission = $permission->where('guard_name','admin-web');
        }
        return view('admin.roles.create', compact('permission'));
    }

    public function store(Request $request)
    {
        $subscriberId = Auth::user()->subscriber_id;

        $this->validate($request, [
            'name' => [
                'required',
                Rule::unique('roles', 'name')->where(function ($query) use ($subscriberId) {
                    if ($subscriberId) {
                        $query->where('subscriber_id', $subscriberId);
                    } else {
                        $query->whereNull('subscriber_id');
                    }
                }),
            ],
            'guard_name' => 'required',
            'permission' => 'required',
        ]);
        // أمنع المشترك من إنشاء رول باسم system_owner أو مشترك آخرين
        if(Auth::user()->subscriber_id && $request->name === 'system_owner'){
            return redirect()->back()->with('error','غير مسموح بإنشاء هذا الدور');
        }
        $role = Role::query()->create([
            'name' => $request->input('name'),
            'guard_name' => $request->input('guard_name'),
            'subscriber_id' => $subscriberId,
        ]);
        $request['permission'] = collect($request['permission'])->map(fn($val)=>(int)$val);
        $role->syncPermissions($request->input('permission'));
        return redirect()->route('admin.roles.index')
            ->with('success', 'تم اضافة الصلاحية بنجاح');
    }


    public function show($id)
    {
        $subscriberId = Auth::user()->subscriber_id;
        $role = Role::query()
            ->where('guard_name', 'admin-web')
            ->when($subscriberId, function ($query) use ($subscriberId) {
                $query->where(function ($q) use ($subscriberId) {
                    $q->where('subscriber_id', $subscriberId)
                        ->orWhereNull('subscriber_id');
                });
            })
            ->findOrFail($id);
        $rolePermissions = Permission::join("role_has_permissions", "role_has_permissions.permission_id", "=", "permissions.id")
            ->where("role_has_permissions.role_id", $id)
            ->get();
        return view('admin.roles.show', compact('role', 'rolePermissions'));
    }


    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $permission = Permission::get();
        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id", $id)
            ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
            ->all();
        return view('admin.roles.edit', compact('role', 'permission', 'rolePermissions'));
    }

    public function update(Request $request, $id)
    {
        $subscriberId = Auth::user()->subscriber_id;
        $this->validate($request, [
            'name' => [
                'required',
                Rule::unique('roles', 'name')->ignore($id)->where(function ($query) use ($subscriberId) {
                    if ($subscriberId) {
                        $query->where('subscriber_id', $subscriberId);
                    } else {
                        $query->whereNull('subscriber_id');
                    }
                }),
            ],
            'permission' => 'required',
        ]);

        $role = Role::query()
            ->where('guard_name', 'admin-web')
            ->when($subscriberId, function ($query) use ($subscriberId) {
                $query->where(function ($q) use ($subscriberId) {
                    $q->where('subscriber_id', $subscriberId)
                        ->orWhereNull('subscriber_id');
                });
            })
            ->findOrFail($id);

        if ($subscriberId && $role->subscriber_id === null) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'غير مسموح بتعديل هذا الدور.');
        }
        $role->name = $request->input('name');
        $role->save();
        $request['permission'] = collect($request['permission'])->map(fn($val)=>(int)$val);
        $role->syncPermissions($request->input('permission'));  
        $Admins = User::role($role->name)->get();   
        foreach($Admins as $Admin){ 
            $Admin->syncPermissions($request->input('permission')); 
        }

        return redirect()->route('admin.roles.index')
        ->with('success', 'تم تعديل الصلاحية بنجاح');
    }

    public function destroy(Request $request)
    {
        $subscriberId = Auth::user()->subscriber_id;
        $role = Role::query()
            ->where('guard_name', 'admin-web')
            ->when($subscriberId, function ($query) use ($subscriberId) {
                $query->where(function ($q) use ($subscriberId) {
                    $q->where('subscriber_id', $subscriberId)
                        ->orWhereNull('subscriber_id');
                });
            })
            ->findOrFail($request->role_id);

        if ($subscriberId && $role->subscriber_id === null) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'غير مسموح بحذف هذا الدور.');
        }

        DB::table('roles')->where('id', $role->id)->delete();
        return redirect()->route('admin.roles.index')
            ->with('success', 'تم حذف الصلاحية بنجاح');
    }

}

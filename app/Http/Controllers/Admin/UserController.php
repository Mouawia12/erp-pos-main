<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; 
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    //
    public function index(){
        $users = DB::table('users')
            ->join('roles' , 'users.role_id' , '=' , 'roles.id')
            ->select('users.*' , 'roles.name_ar as role_ar' , 'roles.name_en as role_en') -> get();

        $roles = Role::all();

        $rolesss = DB::table('role_views')
            -> join('views' , 'role_views.view_id' , '=' , 'views.id')
            ->join('roles' , 'role_views.role_id' , '=' , 'roles.id')
            ->select('role_views.*' , 'views.name_ar as view_name_ar' ,  'views.name_en as view_name_en' ,
                'roles.name_ar as role_name_ar' ,  'roles.name_en as role_name_en' , 'views.route')
            ->where('role_views.role_id' , '=' , Auth::user() -> role_id)
            ->where('role_views.all_auth' , '=' , 1)
            -> get();


        $routes = [] ;
        foreach ($rolesss as $role){
            array_push($routes , $role -> route);
        }
        return view('user.index' , compact('users' , 'roles' , 'routes'));
    }

    public function store(Request $request){
        if ($request -> id == 0){
            $validated = $request->validate([
                'name' => 'required|unique:users',
                'email' => 'required|unique:users',
                'role_id' => 'required',
                'password' => 'required'
            ]);

            User::create([
                'name' => $request -> name,
                'email' => $request -> email ,
                'role_id' => $request -> role_id,
                'password' => Hash::make($request -> password),
            ]);

            return redirect() -> route('users') -> with('success' , __('main.created'));
        } else {
            return  $this -> update($request);
        }


    }
    public function update(Request  $request ){
        $user = User::find($request -> id);
        if($user){
            $user -> update([
                'name' => $request -> name,
                'email' => $request -> email ,
                'role_id' => $request -> role_id,
            ]);

            return redirect() -> route('users') -> with('success' , __('main.updated'));
        }
    }
    public function show($id){
        $user = User::find($id);
        if($user){
            echo json_encode($user);
            exit();
        }
    }
    public function destroy($id){
        $user = User::find($id);
        if($user){
            $user -> delete();
            return redirect() -> route('users') -> with('success' , __('main.deleted'));

        }
    }
}

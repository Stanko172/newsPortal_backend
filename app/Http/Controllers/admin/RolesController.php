<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RolesController extends Controller
{
    public function index(){
        $roles = Role::with('permissions')->get();

        return $roles;
    }

    public function save(Request $request){
        if(isset($request->id)){
            $role = Role::find($request->id);

            foreach($request->permissions as $permission){
                $perm_id = (int) $permission['id'];
                $check = DB::table('permission_role')->where([['permission_id', '=', $perm_id], ['role_id', '=', $request->id]])->get();
                if(count($check) == 0){
                    DB::table('permission_role')->insert([
                        'permission_id' => $permission['id'],
                        'role_id' => $request->id
                    ]);
                }

            }

            $role->title = $request->title;

            if($role->save()){
                return response()->json(['success' => 'Role saved.'], 200);
            }else{
                return response()->json(['error' => "Role is not saved."], 500);
            }
        }else{
            $role = new Role();

            $role->title = $request->title;

            if($role->save()){
                foreach($request->permissions as $permission){
                    DB::table('permission_role')->insert([
                        'permission_id' => $permission['id'],
                        'role_id' => $role->id
                    ]);
    
                }

                return response()->json(['success' => 'Role saved.'], 200);
            }else{
                return response()->json(['error' => "Role is not saved."], 500);
            }
        }

    }

    public function edit(Request $request){
        $perm_id = (int) $request->permission['id'];
        return DB::table('permission_role')->where([['permission_id', '=', $perm_id], ['role_id', '=', $request->id]])->delete();
    }

    public function delete(Request $request){
        $role = Role::find($request->id);

        if($role->delete()){
            return response()->json(['success' => "Role deleted."], 200);
        }else{
            return response()->json(['error' => "Role is not deleted."], 500);
        }
    }
}

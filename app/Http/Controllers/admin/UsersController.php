<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class UsersController extends Controller
{
    public function index(){
        if (! Gate::allows('users_manage_access')) {
            abort(403);
        }

        $users = User::with('roles')->get();

        return $users;
    }

    public function save(Request $request){
        if(isset($request->id)){
            $user = User::find($request->id);

            foreach($request->roles as $role){
                $perm_id = (int) $role['id'];
                $check = DB::table('role_user')->where([['role_id', '=', $perm_id], ['user_id', '=', $request->id]])->get();
                if(count($check) == 0){
                    DB::table('role_user')->insert([
                        'role_id' => $role['id'],
                        'user_id' => $request->id
                    ]);
                }

            }

            $user->name = $request->title;
            $user->email = $request->email;

            if($user->save()){
                return response()->json(['success' => 'user saved.'], 200);
            }else{
                return response()->json(['error' => "user is not saved."], 500);
            }
        }else{
            $user = new User();

            $user->name = $request->title;
            $user->email = $request->email;
            $user->password = $request->password;

            if($user->save()){
                foreach($request->roles as $role){
                    DB::table('role_user')->insert([
                        'role_id' => $role['id'],
                        'user_id' => $user->id
                    ]);
    
                }

                return response()->json(['success' => 'user saved.'], 200);
            }else{
                return response()->json(['error' => "user is not saved."], 500);
            }
        }

    }

    public function edit(Request $request){
        $perm_id = (int) $request->role['id'];
        return DB::table('role_user')->where([['role_id', '=', $perm_id], ['user_id', '=', $request->id]])->delete();
    }

    public function delete(Request $request){
        $user = User::find($request->id);

        if($user->delete()){
            return response()->json(['success' => "user deleted."], 200);
        }else{
            return response()->json(['error' => "user is not deleted."], 500);
        }
    }
}

<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionsController extends Controller
{
    public function index(){
        return Permission::all();
    }

    public function save(Request $request){
        $permission = new Permission();
        if(isset($request->id)){
            $permission = Permission::find($request->id);
        }
        $permission->title = $request->title;
        
        if($permission->save()){
            return response()->json(['success' => 'Permission saved.'], 200);
        }else{
            return response()->json(['error' => "Permission is not saved."], 500);
        }
    }

    public function delete(Request $request){
        $permisson = Permission::find($request->id);
        if($permisson->delete()){
            return response()->json(['success' => "Permission deleted."], 200);
        }else{
            return response()->json(['error' => "Permission is not deleted."], 500);
        }
    }
}

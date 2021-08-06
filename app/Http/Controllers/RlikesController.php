<?php

namespace App\Http\Controllers;

use App\Models\Rlike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RlikesController extends Controller
{
    public function create(Request $request){
        $like = new Rlike;

        $like->reply_id = $request->reply_id;
        $like->user_id = Auth::user()->id;

        if($like->save()){
            return response()->json(['success' => "Like successfully created."], 200);
        }else{
            return response()->json(['error' => "Error while creating like."], 500);
        }
    }

    public function delete(Request $request){
        $like = Rlike::where([['reply_id', "=", $request->reply_id], ['user_id', '=', Auth::user()->id]])->first();

        if($like->delete()){
            return response()->json(['success' => "Like successfully deleted."], 200);
        }else{
            return response()->json(['error' => "Error while deleting like."], 500);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikesController extends Controller
{
    public function create(Request $request){
        $like = new Like();

        $like->comment_id = $request->comment_id;
        $like->user_id = Auth::user()->id;

        if($like->save()){
            return response()->json(['success' => "Like successfully created."], 200);
        }else{
            return response()->json(['error' => "Error while creating like."], 500);
        }
    }

    public function delete(Request $request){
        $like = Like::where([['comment_id', "=", $request->comment_id], ['user_id', '=', Auth::user()->id]])->first();

        if($like->delete()){
            return response()->json(['success' => "Like successfully deleted."], 200);
        }else{
            return response()->json(['error' => "Error while deleting like."], 500);
        }
    }
}

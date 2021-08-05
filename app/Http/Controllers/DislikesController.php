<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Dislike;

class DislikesController extends Controller
{
    public function create(Request $request){
        $dislike = new Dislike();

        $dislike->comment_id = $request->comment_id;
        $dislike->user_id = Auth::user()->id;

        if($dislike->save()){
            return response()->json(['success' => "dislike successfully created."], 200);
        }else{
            return response()->json(['error' => "Error while creating dislike."], 500);
        }
    }

    public function delete(Request $request){
        $dislike = Dislike::where([['comment_id', "=", $request->comment_id], ['user_id', '=', Auth::user()->id]])->first();

        if($dislike->delete()){
            return response()->json(['success' => "dislike successfully deleted."], 200);
        }else{
            return response()->json(['error' => "Error while deleting dislike."], 500);
        }
    }
}

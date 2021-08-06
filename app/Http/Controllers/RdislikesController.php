<?php

namespace App\Http\Controllers;

use App\Models\Rdislike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RdislikesController extends Controller
{
    public function create(Request $request){
        $dislike = new Rdislike();

        $dislike->reply_id = $request->reply_id;
        $dislike->user_id = Auth::user()->id;

        if($dislike->save()){
            return response()->json(['success' => "Dislike successfully created."], 200);
        }else{
            return response()->json(['error' => "Error while creating dislike."], 500);
        }
    }

    public function delete(Request $request){
        $dislike = Rdislike::where([['reply_id', "=", $request->reply_id], ['user_id', '=', Auth::user()->id]])->first();

        if($dislike->delete()){
            return response()->json(['success' => "Dislike successfully deleted."], 200);
        }else{
            return response()->json(['error' => "Error while deleting dislike."], 500);
        }
    }
}

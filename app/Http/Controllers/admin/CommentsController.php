<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentsController extends Controller
{
    public function index(){
        return Comment::with('user')->get();
    }

    public function delete(Request $request){
        $comment = Comment::find($request->id);
        if($comment->delete()){
            return response()->json(['success' => "Comment deleted."], 200);
        }else{
            return response()->json(['error' => "Comment is not deleted."], 500);
        }
    }
}

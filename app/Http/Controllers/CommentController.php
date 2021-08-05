<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Dislike;
use App\Models\Like;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function index(Request $request){
        $comments = Comment::where('article_id', $request->id)->with('replies', 'user', 'likes', 'dislikes')->get();

        $comments->transform(function ($comment){

            if(Auth::user()){
                $is_liked = count(Like::where([['user_id', "=", Auth::user()->id], ["comment_id", "=", $comment->id]])->get()) ;
                $is_disliked = count(Dislike::where([['user_id', "=", Auth::user()->id], ["comment_id", "=", $comment->id]])->get());
            }else{
                $is_liked = 0;
                $is_disliked = 0;
            }

            return collect([
                'id' => $comment->id,
                'content' => $comment->content,
                'author' => $comment->user->name,
                'is_liked' => $is_liked,
                'is_disliked' => $is_disliked,
                'likes' => count($comment->likes),
                'dislikes' => count($comment->dislikes),
                'created_at' => date_format(date_create($comment->created_at), 'Y-m-d H:i:s'),
                'updated_at' => date_format(date_create($comment->updated_at), 'Y-m-d H:i:s'),
                'replies_num' => count($comment->replies)
            ]);
        });

        return $comments;
    }

    public function show(Request $request){
        $comment = Comment::where('id', $request->comment_id)->with('replies', 'user', 'likes', 'dislikes')->first();
       

        return $comment;
    }
}

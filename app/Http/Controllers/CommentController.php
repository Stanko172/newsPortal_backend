<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;

class CommentController extends Controller
{
    public function show(Request $request){
        $comments = Comment::where('article_id', $request->id)->with('replies', 'user')->get();

        $comments->transform(function ($comment){
            $comment->replies_hehe = count($comment->replies);

            return collect([
                'content' => $comment->content,
                'author' => $comment->user->name,
                'likes' => $comment->likes,
                'dislikes' => $comment->dislikes,
                'created_at' => date_format(date_create($comment->created_at), 'Y-m-d H:i:s'),
                'updated_at' => date_format(date_create($comment->updated_at), 'Y-m-d H:i:s'),
                'replies_num' => count($comment->replies)
            ]);
        });

        return $comments;
    }
}

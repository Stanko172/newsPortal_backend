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
        $comments = Comment::where('article_id', $request->id)->with('replies', 'user', 'likes', 'dislikes')->paginate(10);

        $comments->transform(function ($comment){

            if(Auth::user()){
                $is_liked = count(Like::where([['user_id', "=", Auth::user()->id], ["comment_id", "=", $comment->id]])->get()) ;
                $is_disliked = count(Dislike::where([['user_id', "=", Auth::user()->id], ["comment_id", "=", $comment->id]])->get());
                $can_delete = count(Comment::where([['user_id', "=", Auth::user()->id], ["id", "=", $comment->id]])->get()) > 0 ? 1 : 0;
            }else{
                $is_liked = 0;
                $is_disliked = 0;
                $can_delete = 0;
            }

            return collect([
                'id' => $comment->id,
                'content' => $comment->content,
                'author' => $comment->user->name,
                'is_liked' => $is_liked,
                'is_disliked' => $is_disliked,
                'can_delete' => $can_delete,
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

    public function create(Request $request){
        $comment = new Comment;

        $comment->content = $request->content;
        $comment->user_id = Auth::user()->id;
        $comment->article_id = $request->article_id;

        if($comment->save()){
            return response()->json(["data" => $this->single_comment($comment->id)]);
        }else{
            return response()->json(['Error' => 'Error while creating comment.'], 500);
        }
    }

    public function delete(Request $request){
        $comment = Comment::find($request->id);

        if($comment->delete()){
            return response()->json(["Success" => "Comment deleted."], 200);
        }else{
            return response()->json(["Error" => "Error while deleting comment."], 500);
        }
    }

    public function single_comment($id){
        $comment = Comment::where('id', $id)->with('replies', 'user', 'likes', 'dislikes')->first();

        if(Auth::user()){
            $is_liked = count(Like::where([['user_id', "=", Auth::user()->id], ["comment_id", "=", $comment->id]])->get());
            $is_disliked = count(Dislike::where([['user_id', "=", Auth::user()->id], ["comment_id", "=", $comment->id]])->get());
            $can_delete = count(Comment::where([['user_id', "=", Auth::user()->id], ["id", "=", $comment->id]])->get()) > 0 ? 1 : 0;
        }else{
            $is_liked = 0;
            $is_disliked = 0;
            $can_delete = 0;
        }

        $data = new Comment;

        $data->id = $comment->id;
        $data->content = $comment->content;
        $data->author = $comment->user->name;
        $data->is_liked = $is_liked;
        $data->is_disliked = $is_disliked;
        $data->can_delete = $can_delete;
        $data->likes = count($comment->likes);
        $data->dislikes = count($comment->dislikes);
        $data->created_at = date_format(date_create($comment->created_at), 'Y-m-d H:i:s');
        $data->updated_at =date_format(date_create($comment->updated_at), 'Y-m-d H:i:s');
        $data->replies_num = count($comment->replies);

        return $data;
    }
}

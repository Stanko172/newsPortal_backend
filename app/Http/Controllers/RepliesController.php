<?php

namespace App\Http\Controllers;

use App\Events\CommentReplied as EventsCommentReplied;
use App\Models\Comment;
use App\Models\Dislike;
use App\Models\Like;
use App\Models\Rdislike;
use App\Models\Reply;
use App\Models\Rlike;
use App\Models\User;
use App\Notifications\CommentReplied;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RepliesController extends Controller
{
    public function show(Request $request){
        $comment = Comment::where('id', $request->comment_id)->with('user')->first();
        if(Auth::user()){
            $is_liked = count(Like::where([['user_id', "=", Auth::user()->id], ["comment_id", "=", $comment->id]])->get()) ;
            $is_disliked = count(Dislike::where([['user_id', "=", Auth::user()->id], ["comment_id", "=", $comment->id]])->get());
            $can_delete = count(Comment::where([['user_id', "=", Auth::user()->id], ["id", "=", $comment->id]])->get()) > 0 ? 1 : 0;
        }else{
            $is_liked = 0;
            $is_disliked = 0;
            $can_delete = 0;
        }
        $comment->author = $comment->user->name;
        $comment->created_at = date_format(date_create($comment->created_at), 'Y-m-d H:i:s');
        $comment->updated_at = date_format(date_create($comment->updated_at), 'Y-m-d H:i:s');
        $comment->likes_num = count(Like::where('comment_id', $comment->id)->get());
        $comment->dislikes_num = count(Dislike::where('comment_id', $comment->id)->get());;
        $comment->is_liked = $is_liked;
        $comment->is_disliked = $is_disliked;
        $comment->can_delete = $can_delete;
        

        $replies = Reply::where('comment_id', $request->comment_id)->with('user')->get();
        $replies->transform(function ($reply){
            if(Auth::user()){
                $is_liked = count(Rlike::where([['user_id', "=", Auth::user()->id], ["reply_id", "=", $reply->id]])->get()) ;
                $is_disliked = count(Rdislike::where([['user_id', "=", Auth::user()->id], ["reply_id", "=", $reply->id]])->get());
                $can_delete = count(Reply::where([['user_id', "=", Auth::user()->id], ["id", "=", $reply->id]])->get()) > 0 ? 1 : 0;
            }else{
                $is_liked = 0;
                $is_disliked = 0;
                $can_delete = 0;
            }

            return collect([
                'id' => $reply->id,
                'content' => $reply->content,
                'user_id' => $reply->user_id,
                'likes_num' => count(Rlike::where('reply_id', $reply->id)->get()),
                'dislikes_num' => count(Rdislike::where('reply_id', $reply->id)->get()),
                'is_liked' => $is_liked,
                'is_disliked' => $is_disliked,
                'can_delete' => $can_delete,
                'author' => $reply->user->name,
                'created_at' => $reply->created_at,
                'updated_at' => $reply->updated_at
            ]);
        });

        return response()->json(['comment' => $comment, 'replies' => $replies]);
    }

    public function delete(Request $request){
        $reply = Reply::find($request->id);

        if($reply->delete()){
            return response()->json(["Success" => "Reply deleted."], 200);
        }else{
            return response()->json(["Error" => "Error while deleting reply."], 500);
        }
    }

    public function create(Request $request){
        $reply = new Reply;

        $reply->content = $request->content;
        $reply->user_id = Auth::user()->id;
        $reply->comment_id = $request->comment_id;

        if($reply->save()){
            //Stvaranje obavijesti za korisnika da je napravljen odgovor na njegov komentar
            $comment = Comment::find($reply->comment_id);
            $user_comment = User::where('id', $comment->user_id)->first();
            $user_reply = User::where('id', $reply->user_id)->first();
            $user_comment->notify( new CommentReplied($comment, $user_reply) );

            //Odašiljanje događaja
            $msg = "Korisnik " . $user_reply->name . " je odgovorio na Vaš komentar.";
            broadcast(new EventsCommentReplied($msg, $user_comment));
            

            return response()->json(['Success' => 'Reply created'], 200);
        }else{
            return response()->json(['Error' => 'Error while creating reply.'], 500);
        }
    }
}

<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ArticlesController extends Controller
{
    public function index(Request $request){
        $articles = DB::table('articles')
        ->join('categories', 'articles.category_id', '=', 'categories.id')
        ->join('users', 'articles.user_id', '=', 'users.id')
        ->select('articles.*', 'categories.name as category_name', 'users.name as user_name')
        ->orderBy('articles.id', 'asc')
        ->paginate($request->paginate);
        $articles_count = DB::table('articles')->selectRaw('count(*) as articles_count')->pluck('articles_count')->first();
        //Naknadno prepraviti na samo one koji imaju rolu pisca ili urednika
        $authors = User::select('name')->get()->pluck('name');
        $categories = Category::select('name')->where('categories.name', '!=', 'naslovnica')->get()->pluck('name');

        $articles->transform(function ($article){
            $can_action = Auth::user()->id == $article->user_id ? 1 : 0;
            return collect([
                'id' => $article->id,
                'title' => $article->title,
                'category_name' => $article->category_name,
                'user_name' => $article->user_name,
                'created_at' => $article->created_at,
                'can_action' => $can_action
            ]);
        });

        return response()->json([
            'articles' => $articles,
            'articles_count' => $articles_count,
            'authors' => $authors,
            'categories' => $categories
        ]);
    }

    public function delete(Request $request){
        $article = Article::find($request->id);

        if (! Gate::allows('delete_article_specific', $article)) {
            abort(403);
        }

        if($article->delete()){
            response()->json(['success' => 'Article deleted sucessfully.', 200]);
        }else{
            response()->json(['error' => 'Error deleting article.', 500]);
        }
    }
}

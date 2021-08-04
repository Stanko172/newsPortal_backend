<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\ImageUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    public function index(Request $request){
        switch($request->payload){
            case 'Najnovije':
                $articles = Article::with('author', 'category', 'image_uploads')->paginate(16);
                break;
            
            case 'Najčitanije':
                $articles = Article::orderBy('views', 'DESC')->with('author', 'category', 'image_uploads')->paginate(14);
                break;
            
            case 'Preporučeno':
                $articles = Article::orderBy('recommended', 'DESC')->with('author', 'category', 'image_uploads')->paginate(14);
                break;
            
        }
        
        $articles = $articles->getCollection()->transform(
            function ($article){
                $title_image = null;

                foreach($article->image_uploads as $image){
                    if($image->is_title_image === 1){
                        $title_image = $image;
                    }
                }

                return collect([
                    'id' => $article->id,
                    'title' => $article->title,
                    'author' => $article->author->name,
                    'category' => $article->category->name,
                    'views' => $article->views,
                    'recommended' => $article->recommended,
                    'title_image' => $title_image,
                    'created_at' => date_format(date_create($article->created_at), 'Y-m-d H:i:s')
                ])->all();
            }
        );



        return $articles;
    }

    public function create(Request $request){
            
        $request->validate([
           'file' => 'required|mimes:jpg,jpeg,png,csv,txt,xlx,xls,pdf|max:2048'
        ]);

        $fileUpload = new ImageUpload();

        $article = new Article();

        $article->title = $request->title;
        $article->body = $request->body;
        $article->views = 0;
        $article->recommended = $request->recommended;
        $article->user_id = Auth::user()->id;
        $article->category_id = $request->category_id;

        

        if($article->save()){
            if($request->file()) {
                $file_name = time().'_'.$request->file->getClientOriginalName();
                $file_path = $request->file('file')->storeAs('uploads', $file_name, 'public');
    
                $fileUpload->name = time().'_'.$request->file->getClientOriginalName();
                $fileUpload->path = '/storage/' . $file_path;
                $fileUpload->article_id = $article->id;
                $fileUpload->is_title_image = $request->is_title_image;
    
                if($fileUpload->save()){
                    return response()->json(['success'=>['File uploaded successfully.', 'Article created successfully.']], 200);
                }else{
                    return response()->json(['error' => "Couldn\'t save file"], 500);
                }
    
            }
        }else{
            return response()->json(['error' => "Couldn\'t save article!"], 500);
        }
 
    }

    public function show(Request $request){
        $category_id = Category::select('id')->where('name', $request->category_name)->first()->id;
        switch($request->tab){
            case 'Najnovije':
                $articles = Article::with('image_uploads', 'category')->where('category_id', $category_id)->simplePaginate(9);
                break;
            
            case 'Najčitanije':
                $articles = Article::orderBy('views', 'DESC')->with('image_uploads', 'category')->where('category_id', $category_id)->simplePaginate(9);
                break;
            
            case 'Preporučeno':
                $articles = Article::orderBy('recommended', 'DESC')->with('image_uploads', 'category')->where('category_id', $category_id)->simplePaginate(9);
            
        }

        //$articles = Article::select('id', 'title')->with('image_uploads')->where('category_id', $category_id)->simplePaginate(9);

        $articles->transform(function ($article){
            $title_image = null;

            foreach($article->image_uploads as $image){
                if($image->is_title_image === 1){
                    $title_image = $image;
                }
            }

            return collect([
                'id' => $article->id,
                'title' => $article->title,
                'title_image' => $title_image->path,
                'category' => $article->category->name,
                'created_at' => date_format(date_create($article->created_at), 'Y-m-d H:i:s')
            ]);
        });

        return $articles->values();

    }

    public function get_interviews(){
        $interviews = Article::where('category_id', '4')
        ->with('category', 'image_uploads')
        ->take(4)
        ->get();

        $interviews->transform(function ($interview){
            $title_image = null;

            foreach($interview->image_uploads as $image){
                if($image->is_title_image === 1){
                    $title_image = $image;
                }
            }
            return collect(
                [
                    'title' => $interview->title,
                    'title_image' => $title_image->path,
                    'created_at' => date_format(date_create($interview->created_at), 'Y-m-d H:i:s')
                ]
                );
        });

        return $interviews;
    }

    public function search_articles(Request $request){
        $str_search = $request->str_search;
        $articles = Article::select('id', 'title', 'body', 'created_at')->with('image_uploads')->where(function($query) use ($str_search)
        {
            $columns = ['title', 'body'];

            foreach ($columns as $column)
            {
                $query->orWhere($column, 'LIKE', '%'.$str_search.'%');
            }
        })
        ->orderBy('updated_at', 'desc')                            
        ->get();

        $articles->transform(function ($articles){
            $title_image = null;

            foreach($articles->image_uploads as $image){
                if($image->is_title_image === 1){
                    $title_image = $image;
                }
            }
            return collect(
                [
                    'title' => $articles->title,
                    'body' => $articles->body,
                    'title_image' => $title_image->path,
                    'created_at' => date_format(date_create($articles->created_at), 'Y-m-d H:i:s')
                ]
                );
        });

        return $articles;
    }

    public function show_article(Request $request){
        $article = Article::with('category', 'image_uploads', 'author')->where('id', $request->id)->first();

        $title_image = null;

        foreach($article->image_uploads as $image){
            if($image->is_title_image === 1){
                $title_image = $image;
            }
        }

        $article->title_image = $title_image->path;
        $article->article_created_at = date_format(date_create($article->created_at), 'Y-m-d H:i:s');

        return $article;
        
    }
}

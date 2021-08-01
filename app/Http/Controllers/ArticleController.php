<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ImageUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class ArticleController extends Controller
{
    public function index(Request $request){
        switch($request->payload){
            case 'Najnovije':
                $articles = Article::with('author', 'category', 'image_uploads')->paginate(14);
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
                $title_image = "";

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
}

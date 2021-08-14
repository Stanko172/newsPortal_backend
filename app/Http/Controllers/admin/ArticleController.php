<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\ImageUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    public function create(Request $request){
        $article = new Article();
        $article->title = $request->title;
        $article->body = $request->body;
        $article->recommended = $request->recommended;
        $article->views = 0;
        $article->category_id = Category::where('name', $request->category_name)->first()->id;
        $article->user_id = Auth::user()->id;

        $upload_one = true;
        $upload_two = true;

        if($article->save()){
            if($request->hasFile('file')) {
                $file_name = time().'_'.$request->file->getClientOriginalName();
                $file_path = $request->file('file')->storeAs('uploads', $file_name, 'public');
    
                $fileUpload = new ImageUpload();
                $fileUpload->name = time().'_'.$request->file->getClientOriginalName();
                $fileUpload->path = '/storage/' . $file_path;
                $fileUpload->article_id = $article->id;
                $fileUpload->is_title_image = 1;
    
                if($fileUpload->save()){
                   $upload_one = true;
                }else{
                    $upload_one = false;
                }
    
            }
            if($request->hasfile('files')){
                foreach($request->file('files') as $key=>$file)
                {
                    $file_name = time().'_'.$request->file->getClientOriginalName();
                    $file_path = $request->file('file')->storeAs('uploads', $file_name, 'public');
        
                    $fileUpload = new ImageUpload();
                    $fileUpload->name = time().'_'.$request->file->getClientOriginalName();
                    $fileUpload->path = '/storage/' . $file_path;
                    $fileUpload->article_id = $article->id;
                    $fileUpload->is_title_image = 0;

                    if($fileUpload->save()){
                        $upload_two = true;
                    }else{
                        $upload_two = false;
                    }
                }
            }

            if($upload_one == true && $upload_two == true){
                return response()->json(['success' => $article], 200);
            }
        }
        
    }

    public function show(Request $request){
        $article = Article::where('id', $request->id)->with('image_uploads')->first();

        $title_image = null;

        foreach($article->image_uploads as $image){
            if($image->is_title_image === 1){
                $title_image = $image;
            }
        }

        $article->title_image = $title_image;
        $article_category_name = Category::select('name')->where('id', $article->category_id)->first()->name;
        $article->category_name = $article_category_name;

        $categories = Category::select('name')->get()->pluck('name');

        return response()->json(['article' => $article, 'categories' => $categories]);
    }

    public function update(Request $request){
        $article = Article::where('id', $request->id)->first();
        $article->title = $request->title;
        $article->body = $request->body;
        $article->recommended = $request->recommended;
        $article->category_id = Category::where('name', $request->category_name)->first()->id;

        $upload_one = true;
        $upload_two = true;

        if($article->save()){
            if($request->hasFile('file')) {
                $title_img = ImageUpload::where([['article_id', '=', $article->id], ['is_title_image', '=', 1]])->first();
                $path_arr = explode("/", $title_img->name);
                Storage::disk('public')->delete('uploads/' . end($path_arr));

                $title_img->delete();

                $file_name = time().'_'.$request->file->getClientOriginalName();
                $file_path = $request->file('file')->storeAs('uploads', $file_name, 'public');
    
                $fileUpload = new ImageUpload();
                $fileUpload->name = time().'_'.$request->file->getClientOriginalName();
                $fileUpload->path = '/storage/' . $file_path;
                $fileUpload->article_id = $article->id;
                $fileUpload->is_title_image = 1;
    
                if($fileUpload->save()){
                   $upload_one = true;
                }else{
                    $upload_one = false;
                }
    
            }
            if($request->hasfile('files')){
                foreach($request->file('files') as $key=>$file)
                {
                    $file_name = time().'_'.$request->file->getClientOriginalName();
                    $file_path = $request->file('file')->storeAs('uploads', $file_name, 'public');
        
                    $fileUpload = new ImageUpload();
                    $fileUpload->name = time().'_'.$request->file->getClientOriginalName();
                    $fileUpload->path = '/storage/' . $file_path;
                    $fileUpload->article_id = $article->id;
                    $fileUpload->is_title_image = 0;

                    if($fileUpload->save()){
                        $upload_two = true;
                    }else{
                        $upload_two = false;
                    }
                }
            }

            if($upload_one == true && $upload_two == true){
                return response()->json(['success' => $article], 200);
            }
        }
        
    }
}

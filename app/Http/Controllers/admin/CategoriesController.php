<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    public function index(){
        return Category::select('name')->get()->pluck('name');
    }

    public function all(){
        return Category::all();
    }

    public function save(Request $request){
        $category = new Category();
        if(isset($request->id)){
            $category = Category::find($request->id);
        }
        $category->name = $request->name;
        
        if($category->save()){
            return response()->json(['success' => 'Category saved.'], 200);
        }else{
            return response()->json(['error' => "Category is not saved."], 500);
        }
    }

    public function delete(Request $request){
        $permisson = Category::find($request->id);
        if($permisson->delete()){
            return response()->json(['success' => "Category deleted."], 200);
        }else{
            return response()->json(['error' => "Category is not deleted."], 500);
        }
    }
}

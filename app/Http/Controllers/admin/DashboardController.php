<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(){
        $articles_num = DB::table('articles')->count();
        $views_num = (int) DB::table('articles')->sum('articles.views');
        $comments_num = DB::table('comments')->count();
        $users_num = DB::table('users')->count();
        $views_per_category = DB::table('articles')
        ->join('categories', 'articles.category_id', '=', 'categories.id')
        ->selectRaw('sum(views) as sum, categories.name')
        ->where('categories.name', '!=', 'naslovnica')
        ->groupBy('categories.name')
        ->get();
        $articles_per_user = DB::table('articles')
        ->join('users', 'articles.user_id', 'users.id')
        ->select('users.name', 'users.created_at', DB::raw('count(*) as total'))
        ->groupBy('users.name', 'users.created_at')
        ->get();
        $users = User::select('id', 'created_at')
        ->get()
        ->groupBy(function($date) {
            //return Carbon::parse($date->created_at)->format('Y'); // grouping by years
            return Carbon::parse($date->created_at)->format('m'); // grouping by months
        });
        
        $usermcount = [];
        $userArr = [];
        
        foreach ($users as $key => $value) {
            $usermcount[(int)$key] = count($value);
        }

        $months = ['Siječanj', 'Veljača', 'Ožujak', 'Travanj', 'Svibanj', 'Lipanj', 'Srpanj', 'Kolovoz', 'Rujan', 'Listopad', 'Studenti', 'Prosinac'];
        
        for($i = 1; $i <= 12; $i++){
            if(!empty($usermcount[$i])){
                $userArr[] =  (object) array('month' => $months[$i - 1], 'num' => $usermcount[$i]);    
            }else{
                $userArr[] = (object) array('month' => $months[$i - 1], 'num' => 0);    
            }
        }
        $users_chart_data = $userArr;
        $comments = Comment::select('id', 'created_at')
        ->get()
        ->groupBy(function($date) {
            //return Carbon::parse($date->created_at)->format('Y'); // grouping by years
            return Carbon::parse($date->created_at)->format('m'); // grouping by months
        });
        
        $commentmcount = [];
        $commentArr = [];
        
        foreach ($comments as $key => $value) {
            $commentmcount[(int)$key] = count($value);
        }

        $months = ['Siječanj', 'Veljača', 'Ožujak', 'Travanj', 'Svibanj', 'Lipanj', 'Srpanj', 'Kolovoz', 'Rujan', 'Listopad', 'Studenti', 'Prosinac'];
        
        for($i = 1; $i <= 12; $i++){
            if(!empty($commentmcount[$i])){
                $commentArr[] =  (object) array('month' => $months[$i - 1], 'num' => $commentmcount[$i]);    
            }else{
                $commentArr[] = (object) array('month' => $months[$i - 1], 'num' => 0);    
            }
        }
        $comments_chart_data = $commentArr;

        return response()->json([
            'articles_num' => $articles_num, 
            'views_num' => $views_num,
            'comments_num' => $comments_num,
            'users_num' => $users_num,
            'views_per_category' => $views_per_category,
            'articles_per_user' => $articles_per_user,
            'users_chart_data' => $users_chart_data,
            'comments_chart_data' => $comments_chart_data
        ]);
    }
}

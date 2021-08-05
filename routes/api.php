<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post("/register", [RegisterController::class, 'register']);
Route::post("/login", [LoginController::class, 'login']);
Route::post("/logout", [LoginController::class, 'logout']);

Route::get("/abilities", [App\Http\Controllers\AbilitiesController::class, 'index']);

//Auth routes group
Route::prefix('auth')->middleware('auth')->group(function() {

    //Admin routes
    Route::prefix('articles')->group(function () {

        // URL: /auth/articles
        Route::post("/create", [ArticleController::class, 'create']);
    });

});


//Front (No-auth) routes group
Route::prefix('front')->group(function () {

    // URL: /front/...
    Route::post("/articles", [ArticleController::class, 'index']);
    Route::post("/articles/show", [ArticleController::class, 'show']);
    Route::post("/articles/search", [ArticleController::class, 'search_articles']);
    Route::post("/articles/article/show", [ArticleController::class, 'show_article']);
    Route::get("/categories", [CategoryController::class, 'index']);
    Route::get("/interviews", [ArticleController::class, 'get_interviews']);
    Route::post("/comments/index", [App\Http\Controllers\CommentController::class, 'index']);
    Route::post("/comments/show", [App\Http\Controllers\CommentController::class, 'show']);
    Route::post("/comments/create", [App\Http\Controllers\CommentController::class, 'create']);
    Route::post("/comments/delete", [App\Http\Controllers\CommentController::class, 'delete']);

    Route::middleware('auth:sanctum')->post("/likes/create", [App\Http\Controllers\LikesController::class, 'create']);
    Route::middleware('auth:sanctum')->post("/likes/delete", [App\Http\Controllers\LikesController::class, 'delete']);
    Route::middleware('auth:sanctum')->post("/dislikes/create", [App\Http\Controllers\DislikesController::class, 'create']);
    Route::middleware('auth:sanctum')->post("/dislikes/delete", [App\Http\Controllers\DislikesController::class, 'delete']);
    

});

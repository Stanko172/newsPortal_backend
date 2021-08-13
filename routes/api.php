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
//Vratiti middleware('auth') naknadno
Route::prefix('admin')->group(function() {

    //Admin routes
    Route::get("/dashboard/index", [App\Http\Controllers\admin\DashboardController::class, 'index']);
    Route::get("/articles/index", [App\Http\Controllers\admin\ArticlesController::class, 'index']);
    Route::post("/articles/delete", [App\Http\Controllers\admin\ArticlesController::class, 'delete']);

    Route::prefix('articles')->group(function () {

        // URL: /auth/articles
        Route::post("/create", [ArticleController::class, 'create']);
    });

});


//Front (No-auth) routes group
Route::prefix('front')->group(function () {

    // URL: /front/...
    Route::post("/articles", [App\Http\Controllers\ArticleController::class, 'index']);
    Route::post("/articles/show", [App\Http\Controllers\ArticleController::class, 'show']);
    Route::post("/articles/search", [App\Http\Controllers\ArticleController::class, 'search_articles']);
    Route::post("/articles/article/show", [App\Http\Controllers\ArticleController::class, 'show_article']);
    Route::get("/categories", [App\Http\Controllers\CategoryController::class, 'index']);
    Route::get("/interviews", [App\Http\Controllers\ArticleController::class, 'get_interviews']);
    Route::post("/comments/index", [App\Http\Controllers\CommentController::class, 'index']);
    Route::post("/comments/show", [App\Http\Controllers\CommentController::class, 'show']);
    Route::post("/comments/create", [App\Http\Controllers\CommentController::class, 'create']);
    Route::post("/comments/delete", [App\Http\Controllers\CommentController::class, 'delete']);

    Route::middleware('auth:sanctum')->post("/likes/create", [App\Http\Controllers\LikesController::class, 'create']);
    Route::middleware('auth:sanctum')->post("/likes/delete", [App\Http\Controllers\LikesController::class, 'delete']);
    Route::middleware('auth:sanctum')->post("/dislikes/create", [App\Http\Controllers\DislikesController::class, 'create']);
    Route::middleware('auth:sanctum')->post("/dislikes/delete", [App\Http\Controllers\DislikesController::class, 'delete']);
    Route::post("/replies/show", [App\Http\Controllers\RepliesController::class, 'show']);
    Route::post("/replies/delete", [App\Http\Controllers\RepliesController::class, 'delete']);
    Route::post("/replies/create", [App\Http\Controllers\RepliesController::class, 'create']);
    Route::middleware('auth:sanctum')->post("/rlikes/create", [App\Http\Controllers\RlikesController::class, 'create']);
    Route::middleware('auth:sanctum')->post("/rlikes/delete", [App\Http\Controllers\RlikesController::class, 'delete']);
    Route::middleware('auth:sanctum')->post("/rdislikes/create", [App\Http\Controllers\RdislikesController::class, 'create']);
    Route::middleware('auth:sanctum')->post("/rdislikes/delete", [App\Http\Controllers\RdislikesController::class, 'delete']);

    Route::middleware('auth:sanctum')->post("/notifications", [App\Http\Controllers\NotificationsController::class, 'index']);
    Route::middleware('auth:sanctum')->post("/notifications/status", [App\Http\Controllers\NotificationsController::class, 'edit']);
    Route::middleware('auth:sanctum')->post("/notifications/delete", [App\Http\Controllers\NotificationsController::class, 'delete']);
    Route::middleware('auth:sanctum')->get("/notifications/unread_num", [App\Http\Controllers\NotificationsController::class, 'unread_num']);



});

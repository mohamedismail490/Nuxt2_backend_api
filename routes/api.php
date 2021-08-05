<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api;

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

Route::group(['middleware' => ['api', 'auth:api'], 'prefix' => 'auth'], function () {
    Route::post('register', [AuthController::class, 'register'])->withoutMiddleware('auth:api');
    Route::post('login', [AuthController::class, 'login'])->withoutMiddleware('auth:api')->middleware('guest:api');
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('user', [AuthController::class, 'user']);
});

Route::group([ 'middleware' => ['api', 'auth:api']], function () {

    //Topics
    Route::group(['prefix' => 'topics'], function () {
        Route::get('', [Api\TopicController::class, 'index'])->withoutMiddleware('auth:api');
        Route::post('', [Api\TopicController::class, 'store']);
        Route::get('{topic}', [Api\TopicController::class, 'show'])->withoutMiddleware('auth:api');
        Route::get('edit/{topic}', [Api\TopicController::class, 'edit']);
        Route::patch('{topic}', [Api\TopicController::class, 'update']);
        Route::delete('{topic}', [Api\TopicController::class, 'destroy']);
        //Posts
        Route::group(['prefix' => '{topic}/posts'], function () {
            Route::get('', [Api\PostController::class, 'index'])->withoutMiddleware('auth:api');
            Route::post('', [Api\PostController::class, 'store']);
            Route::get('{post}', [Api\PostController::class, 'show'])->withoutMiddleware('auth:api');
            Route::get('edit/{post}', [Api\PostController::class, 'edit']);
            Route::patch('{post}', [Api\PostController::class, 'update']);
            Route::delete('{post}', [Api\PostController::class, 'destroy']);
            //Likes
            Route::post('{post}/likes', [Api\PostController::class, 'toggleLike']);
        });
    });

});

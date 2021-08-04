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

Route::group(['middleware' => 'api', 'prefix' => 'auth'], function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('user', [AuthController::class, 'user']);
});

Route::group([ 'middleware' => 'api'], function () {

    //Topics
    Route::group(['prefix' => 'topics'], function () {
        Route::get('', [Api\TopicController::class, 'index']);
        Route::post('', [Api\TopicController::class, 'store'])->middleware('auth:api');
        Route::get('{topic}', [Api\TopicController::class, 'show']);
        Route::get('edit/{topic}', [Api\TopicController::class, 'edit'])->middleware('auth:api');
        Route::patch('{topic}', [Api\TopicController::class, 'update'])->middleware('auth:api');
        Route::delete('{topic}', [Api\TopicController::class, 'destroy'])->middleware('auth:api');
        //Posts
        Route::group(['prefix' => '{topic}/posts'], function () {
            Route::get('', [Api\PostController::class, 'index']);
            Route::post('', [Api\PostController::class, 'store'])->middleware('auth:api');
            Route::get('{post}', [Api\PostController::class, 'show']);
            Route::get('edit/{post}', [Api\PostController::class, 'edit'])->middleware('auth:api');
            Route::patch('{post}', [Api\PostController::class, 'update'])->middleware('auth:api');
            Route::delete('{post}', [Api\PostController::class, 'destroy'])->middleware('auth:api');
        });
    });

});

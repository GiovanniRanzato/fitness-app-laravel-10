<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CardController;
use App\Http\Controllers\Api\V1\CardDetailController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\ExerciseController;
use App\Http\Controllers\Api\V1\UserController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the 'api' middleware group. Enjoy building your API!
|
*/

Route::get('test', function () { 
    echo 1;
});

// api/v1
// Public  Routes
Route::group(['prefix' => 'v1', 'middleware' => [\Illuminate\Http\Middleware\HandleCors::class]], function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    
});

// api/v1
// Protected Routes
Route::group(['prefix' => 'v1', 'middleware' => ['auth:sanctum']], function () {
    Route::post('logout', [AuthController::class, 'logout']);

    Route::apiResource('users', UserController::class,              ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
    Route::apiResource('categories', CategoryController::class,     ['only' => ['index', 'show', 'store', 'update', 'destroy']] );
    Route::apiResource('cards', CardController::class,              ['only' => ['index', 'show', 'store', 'update', 'destroy']] );
    Route::apiResource('card-details', CardDetailController::class, ['only' => ['store', 'update', 'destroy']] );
    Route::apiResource('exercises', ExerciseController::class,      ['only' => ['index', 'show', 'store', 'update', 'destroy']] );
});


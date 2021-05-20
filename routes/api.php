<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TrackController;

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

Route::prefix('tracks')->group(function () {
    Route::get('/', [TrackController::class, 'index']);
    Route::get('/{track:id}', [TrackController::class, 'show']);
    Route::post('/', [TrackController::class, 'store']);
    Route::put('/{track:id}', [TrackController::class, 'update']);
    Route::delete('/{track:id}', [TrackController::class, 'destroy']);
});

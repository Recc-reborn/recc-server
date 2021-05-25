<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PlaybackController;
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

Route::name('tracks.')->prefix('tracks')->group(function () {
    Route::get('/', [TrackController::class, 'index'])->name('index');
    Route::get('/{track:id}', [TrackController::class, 'show'])->name('show');
    Route::post('/', [TrackController::class, 'store'])->name('store');
    Route::put('/{track:id}', [TrackController::class, 'update'])->name('update');
    Route::delete('/{track:id}', [TrackController::class, 'destroy'])
           ->name('destroy');
});

Route::name('playbacks.')->prefix('playbacks')->group(function () {
    Route::post('/', [PlaybackController::class, 'store'])->name('store');
    Route::delete('/{playback:id}', [PlaybackController::class, 'destroy'])->name('destroy');
});

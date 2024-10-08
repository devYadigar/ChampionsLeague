<?php

use App\Http\Controllers\LeagueController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\PredictionController;
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

// League routes
Route::group(["prefix" => "league"], function () {
    Route::post('/', [LeagueController::class, 'create'])->name('league.create');
    Route::get('/', [LeagueController::class, 'get'])->name('league.get');    
});

// Match routes
Route::group(["prefix" => "match"], function () {
    Route::post('/', [MatchController::class, 'create'])->name('match.create');
    Route::get('/', [MatchController::class, 'get'])->name('match.get');

    // Nested routes for playing matches
    Route::group(['prefix' => 'play'], function () {
        Route::get('/week', [MatchController::class,'playWeek'])->name('match.playWeek');
        Route::get('/all', [MatchController::class,'playAll'])->name('match.playAll');
    });
});

// Prediction routes
Route::get('/prediction', [PredictionController::class,'get'])->name('prediction.get');
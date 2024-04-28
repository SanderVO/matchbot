<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\SeasonController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('home');
});

Route::resource('users', UserController::class)
    ->only([
        'index'
    ]);

Route::resource('events', EventController::class)
    ->only([
        'index',
        'show',
        'create'
    ]);

Route::resource('organizations', OrganizationController::class)
    ->only([
        'index'
    ]);

Route::resource('seasons', SeasonController::class)
    ->only([
        'index'
    ]);

Route::resource('leaderboard', LeaderboardController::class)
    ->only([
        'index'
    ]);

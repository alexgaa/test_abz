<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\PositionController;

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

Route::get('token', [TokenController::class,'get'])->name('token.get');
Route::post('users',[UserController::class, 'store'])->name('users.store');
Route::get('users',[UserController::class, 'getAll'])->name('users.getAll');
Route::get('users/{id}',[UserController::class, 'getById']);
Route::get('positions', [PositionController::class, 'getAll']);

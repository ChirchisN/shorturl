<?php

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

Route::get('/', [\App\Http\Controllers\HomeController::class, 'show'])->name('home');
Route::get('/registration', [\App\Http\Controllers\AuthController::class, 'showRegistration'])->name('showRegistration');
Route::post('/registration', [\App\Http\Controllers\AuthController::class, 'registration'])->name('registration');
Route::get('/login', [\App\Http\Controllers\AuthController::class, 'showLogin'])->name('showLogin');
Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login'])->name('login');

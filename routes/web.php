<?php

use App\Http\Controllers;
use App\Models\User;
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

Route::get('/login', function () {
    $user = User::first();
    auth()->login($user);
    return $user;
});

Route::get('search', Controllers\SearchController::class);
Route::resource('book', Controllers\BookController::class);

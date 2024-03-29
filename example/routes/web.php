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

Route::get('/change-lang/{lang}', '\App\Http\Controllers\PageController@change_lang')->name('lang');
Route::get('/{slug}', '\App\Http\Controllers\PageController@get');

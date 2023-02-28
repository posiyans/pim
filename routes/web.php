<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

//
//Route::get('/', function () {
//    return '/';
//});

//Route::get('/', 'HomeMyController@index');

Route::match(['get'], '/user/avatar/{id?}', 'Ppsd\UserController@userAvatar');

//Auth::routes();

//Route::get('/home', 'HomeMyController@index')->name('home');
Route::get('/migrate', 'PpsdMigrateMyController@migrate');


<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/my-user', function (Request $request) {
    return $request->user();
});


//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});


Route::Post('/login/login', [\App\Http\Controllers\Auth\ApiAuthMyController::class, 'login']);


Route::match(['get', 'options', 'post'], '/login/logout', 'Auth\ApiAuthController@logout');
//Route::match(['get', 'options', 'post'], '/user/info/{id?}', 'Ppsd\UserMyController@info');
Route::match(['get', 'options', 'post'], '/user/list', 'Ppsd\UserController@getList');
Route::match(['options', 'post', 'get'], '/user/avatar/{id?}', 'Ppsd\UserController@userAvatar');
Route::match(['get', 'options'], '/protokol/export/{id}', 'Ppsd\ProtokolController@exportProtokol');
Route::resource('user', 'Ppsd\UserController');
Route::resource('protokol', 'Ppsd\ProtokolController');
Route::resource('partition', 'Ppsd\PartitionController');
Route::resource('report', 'Ppsd\ReportController');
Route::resource('file', 'Ppsd\FileController');
Route::resource('calendar', 'Ppsd\CalendarController');

Route::match(['get', 'options'], '/task/statistic', 'Ppsd\TaskController@getTasksStatistic');
Route::match(['get', 'options', 'post'], '/task/list', 'Ppsd\TaskController@getTasks');
Route::match(['get', 'options', 'post'], '/task/info/{id?}', 'Ppsd\TaskController@getTaskInfo');
Route::resource('task', 'Ppsd\TaskController');
//Route::match(['get', 'options', 'post'], '/protokol/list', 'Ppsd\ProtokolMyController@getProtokols');
//Route::match(['get', 'options', 'post'], '/protokol/source', 'Ppsd\ProtokolMyController@getProtokolSource');

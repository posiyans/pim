<?php

use App\Modules\File\Controlles\DownloadFileController;
use App\Modules\Sms\Contollers\GetSmsBalanceController;
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
Route::Post('/auth/login', [\App\Http\Controllers\Auth\ApiAuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::Get('/auth/logout', [\App\Http\Controllers\Auth\LogoutController::class, 'index']);

    // Задачи для календаря
    Route::Get('/calendar/get-tasks', [\App\Modules\Task\Controllers\GetTasksForCalendarController::class, 'index']);

    // Задачи
    Route::Get('/task/list', [\App\Modules\Task\Controllers\GetTasksListController::class, 'index']);
    Route::Get('/task/get', [\App\Modules\Task\Controllers\GetTaskController::class, 'index']);
    Route::Post('/task/update', [\App\Modules\Task\Controllers\UpdateTaskController::class, 'index']);
    Route::Get('/task/move-to-archive', [\App\Modules\Task\Controllers\MoveTaskToArchiveController::class, 'index']);
    Route::Post('/task/move-date-execution', [\App\Modules\Task\Controllers\MoveDateExecutionController::class, 'index']);
    Route::Post('/task/report/set-done', [\App\Modules\Task\Controllers\SetTaskIsDoneController::class, 'index']);
    Route::Delete('/task/report/delete', [\App\Modules\Task\Controllers\DeleteReportController::class, 'index']);


    Route::match(['get', 'options', 'post'], '/user/list', [\App\Http\Controllers\Ppsd\UserController::class, 'getList']);
    Route::Get('/protocol/list', [\App\Modules\Protocol\Controllers\GetProtocolListController::class, 'index']);
    Route::Get('/protocol/get', [\App\Modules\Protocol\Controllers\GetProtocolController::class, 'index']);
    Route::Post('/protocol/move-to-archive', [\App\Modules\Protocol\Controllers\MoveProtocolToArchiveController::class, 'index']);
    Route::Post('/protocol/update', [\App\Modules\Protocol\Controllers\UpdateProtocolController::class, 'index']);
    Route::Post('/protocol/create', [\App\Modules\Protocol\Controllers\CreateProtocolController::class, 'index']);
    Route::Get('/protocol/get-file', [\App\Modules\Protocol\Controllers\GetProtocolFileController::class, 'index']);
    Route::Post('/test', [\App\Modules\Docx\Controllers\ParseDocxController::class, 'index']);
    Route::Post('/report/create', [\App\Modules\Task\Controllers\CreateReportController::class, 'index']);
    Route::Get('/sms/balance/get', [GetSmsBalanceController::class, 'index']);

    Route::Get('/file/download', [DownloadFileController::class, 'index']);


//    Route::match(['get', 'options', 'post'], '/login/logout', [\App\Http\Controllers\Auth\ApiAuthController::class, 'logout']);
////Route::match(['get', 'options', 'post'], '/user/info/{id?}', 'Ppsd\UserController@info');
//    Route::match(['options', 'post', 'get'], '/user/avatar/{id?}', 'Ppsd\UserController@userAvatar');
//    Route::match(['get', 'options'], '/protokol/export/{id}', 'Ppsd\ProtokolController@exportProtokol');
//    Route::resource('user', 'Ppsd\UserController');
//    Route::resource('protokol', 'Ppsd\ProtokolController');
//    Route::resource('partition', 'Ppsd\PartitionController');
//    Route::resource('report', 'Ppsd\ReportController');
//    Route::resource('file', 'Ppsd\FileController');
//    Route::resource('calendar', 'Ppsd\CalendarController');
//
//    Route::match(['get', 'options'], '/task/statistic', 'Ppsd\TaskController@getTasksStatistic');
//    Route::match(['get', 'options', 'post'], '/task/info/{id?}', 'Ppsd\TaskController@getTaskInfo');
//    Route::resource('task', 'Ppsd\TaskController');
//


//Route::match(['get', 'options', 'post'], '/protokol/list', 'Ppsd\ProtokolController@getProtokols');
//Route::match(['get', 'options', 'post'], '/protokol/source', 'Ppsd\ProtokolController@getProtokolSource');
});

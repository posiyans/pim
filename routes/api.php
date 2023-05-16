<?php

use App\Modules\File\Controlles\DownloadFileController;
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

Route::Post('/auth/login', [\App\Http\Controllers\Auth\LoginController::class, 'index']);
Route::Post('/auth/create-user', [\App\Http\Controllers\Auth\CreateUserController::class, 'index']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::Get('/my-user', [\App\Modules\User\Controllers\GetMyInfoController::class, 'index']);
    Route::Get('/auth/logout', [\App\Http\Controllers\Auth\LogoutController::class, 'index']);
    Route::Get('/user/get-list', [\App\Modules\User\Controllers\GetUsersListController::class, 'index']);
    Route::Get('/user/get', [\App\Modules\User\Controllers\GetUserInfoController::class, 'index']);
    Route::Post('/user/update', [\App\Modules\User\Controllers\UpdateUserController::class, 'index']);
    Route::post('/user/update-field', [\App\Modules\User\Controllers\UpdateUserFieldController::class, 'index']);
    Route::Get('/user/avatar-get', [\App\Modules\User\Controllers\GetUserAvatarController::class, 'index']);
    Route::Post('/user/avatar-upload', [\App\Modules\User\Controllers\UploadUserAvatarController::class, 'index']);
    Route::Post('/user/password-change', [\App\Modules\User\Controllers\ChangeUserPasswordController::class, 'index']);
    Route::Get('/user/get-executors', [\App\Modules\User\Controllers\GetUsersExecutorsController::class, 'index']);
    Route::get('/user/get-last-user-from-telegram', [\App\Modules\Telegram\Controllers\GetLastMessageUserIdController::class, 'index']);
    Route::get('/user/get-two-factor-setting', [\App\Modules\User\Controllers\GetUserTwoFactorSettingController::class, 'index']);
    Route::post('/user/update-two-factor-setting', [\App\Modules\User\Controllers\UpdateUserTwoFactorSettingController::class, 'index']);
    Route::post('/user/change-google-secret-key', [\App\Modules\User\Controllers\ChangeTwoFactorSecretKeyController::class, 'index']);

    // Задачи для календаря
    Route::Get('/calendar/get-tasks', [\App\Modules\Task\Controllers\GetTasksForCalendarController::class, 'index']);

    // Задачи
    Route::Get('/task/list', [\App\Modules\Task\Controllers\GetTasksListController::class, 'index']);
    Route::Get('/task/get', [\App\Modules\Task\Controllers\GetTaskController::class, 'index']);
    Route::Post('/task/update', [\App\Modules\Task\Controllers\UpdateTaskController::class, 'index']);
    Route::Get('/task/move-to-archive', [\App\Modules\Task\Controllers\MoveTaskToArchiveController::class, 'index']);
    Route::Post('/task/move-date-execution', [\App\Modules\Task\Controllers\MoveDateExecutionController::class, 'index']);
    Route::Post('/task/report/set-done', [\App\Modules\Task\Controllers\SetTaskIsDoneController::class, 'index']);
    Route::Post('/task/report/set-as-read', [\App\Modules\Task\Controllers\SetReportAsReadController::class, 'index']);
    Route::Delete('/task/report/delete', [\App\Modules\Task\Controllers\DeleteReportController::class, 'index']);
    Route::Get('/task/report/get-no-read-count', [\App\Modules\Task\Controllers\GetNoReadReportForUserController::class, 'index']);


    Route::Get('/protocol/list', [\App\Modules\Protocol\Controllers\GetProtocolListController::class, 'index']);
    Route::Get('/protocol/get', [\App\Modules\Protocol\Controllers\GetProtocolController::class, 'index']);
    Route::Post('/protocol/move-to-archive', [\App\Modules\Protocol\Controllers\MoveProtocolToArchiveController::class, 'index']);
    Route::Post('/protocol/update', [\App\Modules\Protocol\Controllers\UpdateProtocolController::class, 'index']);
    Route::Post('/protocol/create', [\App\Modules\Protocol\Controllers\CreateProtocolController::class, 'index']);
    Route::Post('/protocol/create-type', [\App\Modules\Protocol\Controllers\CreateTypeProtocolController::class, 'index']);
    Route::Get('/protocol/get-type', [\App\Modules\Protocol\Controllers\GetTypesProtocolController::class, 'index']);

    Route::Post('/protocol/parse-file', [\App\Modules\Docx\Controllers\ParseDocxController::class, 'index']);

    Route::Post('/report/create', [\App\Modules\Task\Controllers\CreateReportController::class, 'index']);
//    Route::Get('/sms/balance/get', [GetSmsBalanceController::class, 'index']);

    Route::Get('/file/download', [DownloadFileController::class, 'index']);
    Route::Post('/file/upload', [\App\Modules\File\Controlles\UploadFileController::class, 'index']);
    Route::Get('/file/delete', [\App\Modules\File\Controlles\DeleteFileController::class, 'index']);


    Route::Get('/log/task/get', [\App\Modules\Log\Controllers\GetTaskLogController::class, 'index']);
    Route::Get('/log/protocol/get', [\App\Modules\Log\Controllers\GetProtocolLogController::class, 'index']);

    Route::Get('/setting/get-telegram-bot-token', [\App\Modules\GlobalOptions\Controllers\GetTelegramTokenController::class, 'index']);
    Route::Post('/setting/update-telegram-bot-token', [\App\Modules\GlobalOptions\Controllers\UpdateTelegramTokenController::class, 'index']);
    Route::Get('/telegram/get-bot-info', [\App\Modules\Telegram\Controllers\GetBotInfoController::class, 'index']);
    Route::Get('/setting/get-mail', [\App\Modules\GlobalOptions\Controllers\GetMailSettingController::class, 'index']);
    Route::Post('/setting/update-mail', [\App\Modules\GlobalOptions\Controllers\UpdateMailSettingController::class, 'index']);
    Route::Post('/setting/send-test-mail', [\App\Modules\GlobalOptions\Controllers\SendTestMailController::class, 'index']);
    Route::Post('/setting/change-two-factor-enable', [\App\Modules\GlobalOptions\Controllers\ChangeTwoFactorEnableController::class, 'index']);
});

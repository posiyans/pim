<?php

namespace App\Modules\Task\Controllers;

use App\Http\Controllers\MyController;
use App\Modules\Log\Classes\CreateInfoLog;
use App\Modules\Task\Models\ViewReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class SetTaskIsDoneController extends MyController
{

    public function index(Request $request)
    {
        $id = $request->id;
        $report = ViewReport::find($id);
        if ($report) {
            $user = Auth::user();
            if ($report->user_id == $user->id or $user->moderator) {
                $task = $report->task;
                $now = date("Y-m-d H:i:s");
                $task_executor = '';
                if ($report->user_id != $user->id) {
                    $task_executor = 'для ' . $report->user->name;
                }
                if (isset($report->done)) {
                    $report->done = null;
                    $text_log = 'Отмена закрытия задачи ' . $task_executor;
                } else {
                    $report->done = $now;
                    $text_log = 'Закрыл задачу ' . $task_executor;
                }
                $task->save();
                $report->save();
                (new CreateInfoLog($task))->text($text_log)->run();
            }
        }
        Cache::flush();
        return response([]);
    }


}

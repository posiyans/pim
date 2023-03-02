<?php

namespace App\Modules\Task\Controllers;

use App\Http\Controllers\MyController;
use App\Models\VievReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SetTaskIsDoneController extends MyController
{

    public function index(Request $request)
    {
        $id = $request->id;
        $report = VievReport::find($id);
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
                    $task->history .= '<i>' . $now . '</i> ' . $user->name . ' отменил закрытие задачи ' . $task_executor . '</br>';
                } else {
                    $report->done = $now;
                    $task->history .= '<i>' . $now . '</i> ' . $user->name . ' закрыл задачу ' . $task_executor . '</br>';
                }
                $task->save();
                $report->save();
            }
        }
        return response([]);
    }


}

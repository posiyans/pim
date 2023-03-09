<?php

namespace App\Modules\Task\Controllers;

use App\Http\Controllers\MyController;
use App\Modules\Log\Models\Log;
use App\Modules\Task\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MoveDateExecutionController extends MyController
{

    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user->moderator) {
            $id = $request->id;
            $task = Task::find($id);
            $task_old = clone $task;
            $newDate = $request->date;
            if ($newDate == null) {
                $task->data_perenosa = null;
            } else {
                $task->data_perenosa = date('Y-m-d', strtotime($newDate));
            }
//            $hist = $task->history;
//            $today = date(/**/'Y-m-d H:m:s');
//            $text = '<i>' . $today . '</i> ' . $user->name . ' перенес дату исполнения на' . $task->data_perenosa . '<br>';
//            $task->history = $hist . $text;
            $task->save();
            Log::saveDiff($task, $task_old, 'Task transfer Date');
            $log = new Log();
            $log->user_id = $user->id;
            $log->value = [
                'text' => $user->name . ' перенос даты исполнения на ' . $task->data_perenosa,
            ];
            $log->type = 'info';
            $task->log()->save($log);
        }
        return response(true);
    }


}

<?php

namespace App\Modules\Task\Controllers;

use App\Http\Controllers\MyController;
use App\Modules\Log\Models\Log;
use App\Modules\Task\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MoveTaskToArchiveController extends MyController
{

    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user->moderator) {
            $id = $request->id;
            $task = Task::find($id);
            $task_old = clone $task;
            if ($task) {
                $today = date('Y-m-d H:m:s');
                $text = '<i>' . $today . '</i> ' . $user->name . ' перенес задачу в архив<br>';
                $task->arxiv = $text;
                $task->save();
                Log::saveDiff($task, $task_old, 'task to archiv');
                return response('ok');
            }
        }
        return response();
    }


}

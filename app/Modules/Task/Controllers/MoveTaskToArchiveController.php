<?php

namespace App\Modules\Task\Controllers;

use App\Http\Controllers\MyController;
use App\Modules\Log\Classes\CreateInfoLog;
use App\Modules\Log\Models\Log;
use App\Modules\Task\Models\Task;
use Illuminate\Http\Request;

class MoveTaskToArchiveController extends MyController
{

    public function __construct()
    {
        $this->middleware('only_moderator');
    }

    public function index(Request $request)
    {
        $id = $request->id;
        $task = Task::find($id);
        $task_old = clone $task;
        if ($task) {
            $today = date('Y-m-d H:m:s');
            $text = '<i>' . $today . '</i> перенес задачу в архив<br>';
            $task->arxiv = $text;
            $task->save();
            Log::saveDiff($task, $task_old, 'task to archiv');
            (new CreateInfoLog($task))->text('Перенес задачу в архив')->run();
            return response('ok');
        }

        return response();
    }


}

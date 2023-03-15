<?php

namespace App\Modules\Task\Controllers;

use App\Http\Controllers\MyController;
use App\Modules\Log\Classes\CreateInfoLog;
use App\Modules\Log\Models\Log;
use App\Modules\Task\Models\Task;
use App\Modules\Task\Models\ViewReport;
use Illuminate\Http\Request;

class UpdateTaskController extends MyController
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
            $task->text = $request->text ?? $task->text;
            $task->executor = $request->executor ?? $task->executor;
            $task->number = $request->number ?? $task->number;
            $user = $request->users;
            $task->data_ispoln = $request->data_ispoln;
            $task->save();
            foreach ($task->viewReport as $item) {
                if ($item->executor == 1) {
                    if (in_array($item->user_id, $user)) {
                        unset($user[array_search($item->user_id, $user)]);
                    } else {
                        $item->delete();
                    }
                }
            }
            foreach ($user as $item) {
                $executor = new ViewReport();
                $executor->user_id = (int)$item;
                $executor->executor = 1;
                $task->viewReport()->save($executor);
            }
            (new CreateInfoLog($task))->text('Измененил задачу')->run();
        }
        Log::saveDiff($task, $task_old);
        return response(['user' => $user, 'task' => $task]);
    }


}

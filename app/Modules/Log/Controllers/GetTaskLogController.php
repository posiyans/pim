<?php

namespace App\Modules\Log\Controllers;


use App\Http\Controllers\MyController;
use App\Modules\Task\Models\Task;
use Illuminate\Http\Request;

use function App\Modules\Docx\Controllers\mb_strtolower;

class GetTaskLogController extends MyController
{


    public function index(Request $request)
    {
        $task_id = $request->id;
        $task = Task::find($task_id);
        $log = $task->log;
        return response($log);
    }

}

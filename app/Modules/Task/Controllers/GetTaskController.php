<?php

namespace App\Modules\Task\Controllers;

use App\Http\Controllers\MyController;
use App\Modules\Task\Models\Task;
use Illuminate\Http\Request;

class GetTaskController extends MyController
{

    public function index(Request $request)
    {
        $data = [];
        $id = $request->id;
        if ($id) {
            $id = (int)$id;
            $task = Task::find($id);

            if (isset($task) and $task->isAccess()) {
                $task->protokol;
                $task->partition;
                if ($request->showdeleted == 'true') {
                    $task->allReportAndUser();
                } else {
                    $task->reportAndUser();
                }
                $data = $task;
                $data->executors = $task->getExecutor();
            }
        }


        return response($data);
    }


}

<?php

namespace App\Modules\Task\Controllers;

use App\Http\Controllers\MyController;
use App\Modules\Task\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GetTaskController extends MyController
{

    public function index(Request $request)
    {
        $data = false;
        $id = $request->id;
        $user = Auth::user();
        if ($id) {
            $id = (int)$id;
            $task = Task::find($id);

            if (isset($task) and $task->isAccess()) {
                $task->protokol;
                $task->partition;
                if ($request->showdeleted == 'true' && $user->moderator) {
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

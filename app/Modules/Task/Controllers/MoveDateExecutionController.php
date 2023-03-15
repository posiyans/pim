<?php

namespace App\Modules\Task\Controllers;

use App\Http\Controllers\MyController;
use App\Modules\Log\Classes\CreateInfoLog;
use App\Modules\Task\Models\Task;
use Illuminate\Http\Request;

class MoveDateExecutionController extends MyController
{
    public function __construct()
    {
        $this->middleware('only_moderator');
    }

    public function index(Request $request)
    {
        $id = $request->id;
        $task = Task::find($id);
        $newDate = $request->date;
        if ($newDate == null) {
            $task->data_perenosa = null;
        } else {
            $task->data_perenosa = date('Y-m-d', strtotime($newDate));
        }
        $task->save();
        (new CreateInfoLog($task))->text('Перенос даты исполнения на ' . $task->data_perenosa)->run();
        return response(true);
    }


}

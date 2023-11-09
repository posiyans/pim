<?php

namespace App\Modules\Task\Controllers;

use App\Http\Controllers\MyController;
use App\Modules\Task\Models\Task;
use App\Modules\Task\Repositories\TaskRepository;
use App\Modules\Task\Resources\TaskResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GetTasksListController extends MyController
{
    //
    public function index(Request $request)
    {
        $user = Auth::user();
//        $user = User::find(13);
        $query = Task::query();
        $limit = (int)$request->limit;
        $queryNew = (new TaskRepository())
            ->archiv($request->archiv);

        if ($user->moderator) {
            if ($request->executor) {
                $executors = $request->executor;
                if (is_string($executors)) {
                    $executors = [$executors];
                }
                $queryNew->executors($executors);
            }
        } else {
            $executors = $user->aliases;
            array_push($executors, $user->id);
            if ($request->executor) {
                $executors = in_array($request->executor, $executors) ? [$request->executor] : $executors;
            }
            $queryNew->executors($executors);
        }
        if ($request->today) {
            $queryNew->today();
        }
        if ($request->title) {
            $queryNew->find($request->title);
        }
        if ($request->status) {
            $queryNew->status($request->status);
        }
        $newTask = $queryNew->paginate($request->limit);
        return TaskResource::collection($newTask)->response()->getData(true);
    }


}

<?php

namespace App\Http\Controllers\Ppsd;

use App\Http\Controllers\MyController;
use App\Models\ViewReport;
use App\Modules\Log\Models\Log;
use App\Modules\Task\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends MyController
{
    //
    public function getTasks(Request $request)
    {
        $user = Auth::user();

        $query = Task::query();
        $limit = (int)$request->limit;
        if (isset($request->archiv) && $request->archiv == 'true') {
            $query->whereNotNull('arxiv');
        } else {
            $query->whereNull('arxiv');
        }
//        if (!$user->hasRole('admin')) {
        $executor = $user->aliases;
        array_push($executor, $user->id);
        $task = ViewReport::where('executor', 1)->whereIn('user_id', $executor)->pluck('task_id')->toArray();
        $query->whereIn('id', $task);
//        }
        if ($request->executor) {
            $executor = $request->executor;
            $task = ViewReport::where('executor', 1)->where('user_id', $executor)->pluck('task_id')->toArray();
            $query->whereIn('id', $task);
        }
        if ($request->today) {
            $today = date('Y-m-d');
            $query->where(function ($query) use ($today) {
                $query->where('data_ispoln', $today)
                    ->orWhere('data_perenosa', $today);
            });

            //$query->where('data_ispoln',$today)->orWhere('data_perenosa',$today);
            //$query->where('data_perenosa',$today);
        }
        if ($request->title) {
            $find = explode(' ', $request->title);

            foreach ($find as $value) {
                $query->where('text', 'like', '%' . $value . '%');
            }
        }
        if ($request->sort) {
            if ($request->sort == '+id') {
                $query->orderBy('id', 'desc')->orderBy('protokol_id', 'desc');
            } else {
                $query->orderBy('id', 'asc')->orderBy('protokol_id', 'asc');
            }
        }
        $tasks = $query->paginate($limit);
        $total = $tasks->total();
        foreach ($tasks as $task) {
            $task->protokol;
            $last_report = $task->report->last();
            $task->executor;
            $task->last_report = '';
            if ($last_report) {
                if ($last_report->file_name) {
                    $task->last_report = 'Добавлен файл';
                }
                if ($last_report->text) {
                    $task->last_report = $last_report->text;
                }
            }

            $task->execution = $task->getPercentComplete();
        }

        return response(['total' => $total, 'items' => $tasks]);
    }

    public function getTaskInfo($id = false, Request $request)
    {
        $data = [];
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

    public function getTasksStatistic()
    {
        $query = Task::where('arxiv', null);
        $user = Auth::user();
        if (!$user->hasRole('admin')) {
            $executor = $user->aliases;
            array_push($executor, $user->id);
            $task = ViewReport::where('executor', 1)->whereIn('user_id', $executor)->pluck('task_id')->toArray();
            $query->whereIn('id', $task);
        }
        $tasks = $query->with('viewReport')->get();
        $done = 0;
        $overdueTask = 0;
        $inWork = 0;
        $migratedTask = 0;
        $overdueMigratedTask = 0;
        $today = date('Y-m-d');
        foreach ($tasks as $task) {
            $PercentComplete = $task->getPercentComplete();
            if ($PercentComplete == 100) {
                $done++;
            } else {
                if ($task->data_perenosa == null) {
                    if ($task->data_ispoln < $today) {
                        $overdueTask++;
                    } else {
                        $inWork++;
                    }
                } else {
                    if ($task->data_perenosa < $today) {
                        $overdueMigratedTask++;
                    } else {
                        $migratedTask++;
                    }
                }
            }
        }

        $data = [
            ['value' => $overdueTask, 'name' => 'Просроченные задачи'],
            ['value' => $inWork, 'name' => 'В работе'],
            //[ 'value' => $done, 'name' => 'Выполнено' ],
            ['value' => $migratedTask, 'name' => 'Перенесенные задачи'],
            ['value' => $overdueMigratedTask, 'name' => 'Перенесенные задачи в работе']
        ];
        $legend = [];
        foreach ($data as $item) {
            $legend[] = $item['name'];
        }
        return response(['data' => $data, 'legend' => $legend]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->hasRole('admin')) {
            $action = $request->type;
            if ($action == 'taskToArchiv') {
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
                return response(['taskToArchiv']);
            }
            if ($action == 'updateTask') {
                $task = Task::find($id);
                $task_old = clone $task;
                $request_task = $request->task;
                if ($task and $request_task['id'] == $task->id) {
                    $today = date('Y-m-d H:m:s');
                    $text = '<i>' . $today . '</i> ' . $user->name . ' перенес задачу в архив<br>';
                    $task->text = $request_task['text'];
                    $task->executor = $request_task['executor'];
                    $task->number = $request_task['number'];
                    $user = $request_task['users'];
                    if ($request_task['time_ispoln'] == null) {
                        $task->data_ispoln = null;
                    } else {
                        $task->data_ispoln = date('Y-m-d', strtotime($request_task['time_ispoln']));
                    }
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
                }
                Log::saveDiff($task, $task_old);
                return response(['user' => $user, 'task' => $task]);
            }
            if ($action == 'transferDate') {
                $task = Task::find($id);
                $task_old = clone $task;
                $request_task = $request->task;
                if ($request_task['time_transfer'] == null) {
                    $task->data_perenosa = null;
                } else {
                    $task->data_perenosa = date('Y-m-d', strtotime($request_task['time_transfer']));
                }
                $hist = $task->history;
                $today = date('Y-m-d H:m:s');
                $text = '<i>' . $today . '</i> ' . $user->name . ' перенес дату исполнения на ' . $task->data_perenosa . '<br>';
                $task->history = $hist . $text;
                $task->save();
                Log::saveDiff($task, $task_old, 'Task transfer Date');
            }
        }
        return response([]);
    }


}

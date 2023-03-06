<?php

namespace App\Modules\Task\Controllers;

use App\Http\Controllers\MyController;
use App\Models\ViewReport;
use App\Modules\Task\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GetTasksListController extends MyController
{
    //
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Task::query();
        $limit = (int)$request->limit;
        if (isset($request->archiv) && $request->archiv == 'true') {
            $query->whereNotNull('arxiv');
        } else {
            $query->whereNull('arxiv');
        }
        if (!$user->moderator) {
            $executor = $user->aliases;
            array_push($executor, $user->id);
            $task = ViewReport::where('executor', 1)->whereIn('user_id', $executor)->pluck('task_id')->toArray();
            $query->whereIn('id', $task);
        }
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
        }
        if ($request->title) {
            $find = explode(' ', $request->title);
            foreach ($find as $value) {
                $query->whereRaw("(lower(concat_ws(' ',id, text))) like '%" . strtolower($value) . "%'");
            }
        }
        if ($request->sort) {
            if ($request->sort == '+id') {
                $query->orderBy('id', 'desc')->orderBy('protocol_id', 'desc');
            } else {
                $query->orderBy('id', 'asc')->orderBy('protocol_id', 'asc');
            }
        }
        $tasks = $query->paginate($limit);
        foreach ($tasks as $task) {
            $protokol = $task->protokol;
            $task->protokol = [
                'number' => $protokol->number
            ];
            $last_report = $task->report->last();
            if ($last_report) {
                $last_report->files;
            }
            $task->last_report = $last_report;
//            if ($last_report) {
//                if ($last_report->text) {
//                    $task->last_report = $last_report->text;
//                }
//                if ($last_report->file) {
//                    $task->last_report = 'Добавлен файл';
//                }
//            }
//
            $task->execution = $task->getPercentComplete();
        }

        return response($tasks);
    }


}

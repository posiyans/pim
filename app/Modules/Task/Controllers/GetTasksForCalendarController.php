<?php

namespace App\Modules\Task\Controllers;

use App\Http\Controllers\MyController;
use App\Models\VievReport;
use App\Modules\Task\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GetTasksForCalendarController extends MyController
{
    //
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Task::query();
        if (isset($request->archiv) && $request->archiv == 'true') {
            $query->whereNotNull('arxiv');
        } else {
            $query->whereNull('arxiv');
        }
        if (!$user->moderator) {
            $executor = $user->aliases;
            array_push($executor, $user->id);
            $task = VievReport::where('executor', 1)->whereIn('user_id', $executor)->pluck('task_id')->toArray();
            $query->whereIn('id', $task);
        }
        if ($request->executor) {
            $executor = $request->executor;
            $task = VievReport::where('executor', 1)->where('user_id', $executor)->pluck('task_id')->toArray();
            $query->whereIn('id', $task);
        }
        $date_start = $request->date_start;
        $date_end = $request->date_end;
        $query->where('data_ispoln', '>=', $date_start);
        $query->where(function ($query) use ($date_start, $date_end) {
            $query->where(function ($query) use ($date_start, $date_end) {
                $query->whereBetween('data_ispoln', [$date_start, $date_end]);
            })
                ->orWhere(function ($query) use ($date_start, $date_end) {
                    $query->whereBetween('data_perenosa', [$date_start, $date_end]);
                });
        });
        $tasks = $query->get();
        foreach ($tasks as $task) {
            $protokol = $task->protokol;
            $task->protokol = [
                'number' => $protokol->number
            ];
            $last_report = $task->report->last();
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

        return response($tasks);
    }


}

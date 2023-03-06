<?php

namespace App\Http\Controllers\Ppsd;

use App\Http\Controllers\Controller;
use App\Models\ViewReport;
use App\Modules\Task\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Task::query();
        $start = str_replace('"', '', $request->start);
        $end = str_replace('"', '', $request->end);
        if (!$user->hasRole('admin')) {
            $executor = $user->aliases;
            array_push($executor, $user->id);
            $task = ViewReport::where('executor', 1)->whereIn('user_id', $executor)->pluck('task_id')->toArray();
            $query->whereIn('id', $task);
        }
        $query->where(function ($query) use ($start) {
            $query->where('data_ispoln', '>=', $start)
                ->orWhere('data_perenosa', '>=', $start);
        });
        $query->where(function ($query) use ($end) {
            $query->where('data_ispoln', '<', $end)
                ->orWhere('data_perenosa', '<', $end);
        });
        $tasks = $query->get();
        $today = date('Y-m-d');
        foreach ($tasks as $task) {
            $task->protokol;
            $task->partition;
            $task->executors = $task->getExecutor();
            $task->title = $task->executor;
            $task->start = $task->data_ispoln;
            $task->color = '#Ffbc9a';
            $task->done = $task->getPercentComplete();
            if (($task->data_ispoln > $today) or ($task->data_perenosa > $today)) {
                $task->color = '#cccccc';
            }
            if ($task->done == 100) {
                $task->color = '#CCFFCC';
            }
            if ($task->data_perenosa != null) {
                $task->color = '#FF1493';
                $task->start = $task->data_perenosa;
                $p = substr_count($task->history, 'перенес');
                if ($p > 1) {
                    $task->title = $task->title . ' (' . $p . ' переноса)';
                    $task->color = '#FF0000';
                }
            }
        }
        return response(['data' => $tasks]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return response('store');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        return response('show');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        return response('edit');
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

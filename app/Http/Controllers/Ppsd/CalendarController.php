<?php

namespace App\Http\Controllers\Ppsd;

use App\Http\Models\Task;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
        $query = Task::query();
        $start = $request->start;
        $end = $request->end;
        $query->where(function ($query) use ($start) {
            $query->where('data_ispoln','>=',$start)
                ->orWhere('data_perenosa','>=',$start);
        });
        $query->where(function ($query) use ($end) {
            $query->where('data_ispoln','<',$end)
                ->orWhere('data_perenosa','<',$end);
        });
        $tasks = $query->get();
        $json = [];
        $today = date('Y-m-d');
        foreach ($tasks as $task){
            $task->protokol;
            $task->partition;
            $task->executors = $task->getExecutor();
            $task->title = $task->executor;
            $task->start = $task->data_ispoln;
            $task->color = '#Ffbc9a';
            $task->done= $task->getPercentComplete();
            if (($task->data_ispoln > $today) or ($task->data_perenosa  > $today)){
                $task->color = '#cccccc';
            }
            if ($task->done == 100){
                $task->color = '#CCFFCC';
            }
            if ($task->data_perenosa != null){
                $task->color = '#FF1493';
                $task->start = $task->data_perenosa;
            }
            $json[] = [$tasks];
        }
        return $this->response($tasks);
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
        return $this->response('store');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        return $this->response('show');
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
        return $this->response('edit');
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

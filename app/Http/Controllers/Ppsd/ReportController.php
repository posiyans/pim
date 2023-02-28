<?php

namespace App\Http\Controllers\Ppsd;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\Report;
use App\Models\VievReport;
use App\Modules\Log\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return response('index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        echo 'create';
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ((Input::has('message') or Input::hasFile('file')) and Input::has('task')) {
            $report = new Report();
            $report->task_id = Input::get('task');
            $report->user_id = Auth::id();
            if (Input::has('message')) {
                $report->text = Input::get('message');
            }
            $report->save();
            if (Input::file('file')) {
                $inputFile = Input::file('file');
                $md5 = $this->md5_file($inputFile);
                $inputFile->move($md5['folder'], $md5['md5']);
                $file = new File();
                $file->hash = $md5['md5'];
                $file->name = $inputFile->getClientOriginalName();
                $report->file()->save($file);
            }
        }
        return response([$report]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        //
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
        $report = VievReport::find($id);
        if ($report) {
            $user = Auth::user();
            if ($report->user_id == $user->id or $user->hasRole('admin')) {
                $task = $report->task;
                $now = date("Y-m-d H:i:s");
                $task_executor = '';
                if ($report->user_id != $user->id) {
                    $task_executor = 'для ' . $report->user->name;
                }
                if (isset($report->done)) {
                    $report->done = null;
                    $task->history .= '<i>' . $now . '</i> ' . $user->name . ' отменил закрытие задачи ' . $task_executor . '</br>';
                } else {
                    $report->done = $now;
                    $task->history .= '<i>' . $now . '</i> ' . $user->name . ' закрыл задачу ' . $task_executor . '</br>';
                }
                $task->save();
                $report->save();
            }
        }
        return response([]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $access = false;
        $user = Auth::user();
        $report = Report::find($id);
        if ($report) {
            if ($report->user_id == $user->id or $user->hasRole('admin')) {
                $access = true;
            }
            if ($access) {
                $log = new Log();
                $log->description = 'delete Report';
                $log->value = $report;
                $log->type = 'ok';
                $report->log()->save($log);
                $report->delete();
            } else {
                $log = new Log();
                $log->description = 'Access is denied';
                $log->value = $report;
                $log->type = 'alert';
                $report->log()->save($log);
            }
        }
        return response([$id]);
    }
}

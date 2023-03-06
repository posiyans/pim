<?php

namespace App\Modules\Task\Controllers;

use App\Http\Controllers\MyController;
use App\Modules\File\Models\File;
use App\Modules\Task\Classes\NewReportNotification;
use App\Modules\Task\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CreateReportController extends MyController
{

    public function index(Request $request)
    {
        if (($request->has('message') or $request->hasFile('file')) and $request->has('task')) {
            $report = new Report();
            $report->task_id = $request->get('task');
            $report->user_id = Auth::id();
            if ($request->has('message')) {
                $report->text = $request->get('message');
            }
            $report->save();
            if ($request->file('file')) {
                $inputFile = $request->file('file');
                $md5 = $this->md5_file($inputFile);
                $inputFile->move($md5['folder'], $md5['md5']);
                $file = new File();
                $file->hash = $md5['md5'];
                $file->name = $inputFile->getClientOriginalName();
                $report->files()->save($file);
            }
            (new NewReportNotification($report))->run();
            // todo обновить всем подписчикам (кроме себя) что есть новое сообщение
        }
        return response([$report]);
    }


}

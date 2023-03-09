<?php

namespace App\Modules\Task\Controllers;

use App\Http\Controllers\MyController;
use App\Modules\File\Models\File;
use App\Modules\File\Repositories\FileRepository;
use App\Modules\Task\Classes\NewReportNotification;
use App\Modules\Task\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
            $inputFile = $request->file('file');
            if ($inputFile) {
                $md5 = md5_file($inputFile->getRealPath());
                $path = FileRepository::getPathFromHash($md5, true);
                Storage::putFileAs($path, $inputFile, $md5);
                $file = new File();
                $file->name = $inputFile->getClientOriginalName();
                $file->hash = $md5;
                $report->files()->save($file);
            }
            (new NewReportNotification($report))->run();
        }
        return response([$report]);
    }


}

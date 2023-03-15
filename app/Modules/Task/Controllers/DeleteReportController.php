<?php

namespace App\Modules\Task\Controllers;

use App\Http\Controllers\MyController;
use App\Modules\Log\Classes\CreateInfoLog;
use App\Modules\Task\Models\Report;
use Illuminate\Http\Request;

class DeleteReportController extends MyController
{

    public function __construct()
    {
        $this->middleware('only_moderator');
    }

    public function index(Request $request)
    {
        $id = $request->id;
        $report = Report::find($id);
        if ($report) {
            $report->delete();
            $log_text = 'Удалил отчет ' . $report->user->name . ' от ' . $report->created_at;
            (new CreateInfoLog($report->task))->text($log_text)->run();
            return response(true);
        }
        return response('', 404);
    }


}

<?php

namespace App\Modules\Task\Controllers;

use App\Http\Controllers\MyController;
use App\Modules\Log\Classes\CreateInfoLog;
use App\Modules\Log\Models\Log;
use App\Modules\Task\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeleteReportController extends MyController
{

    public function index(Request $request)
    {
        $access = false;
        $user = Auth::user();
        $id = $request->id;
        $report = Report::find($id);
        if ($report) {
            if ($user->moderator) {
                $access = true;
            }
            if ($access) {
                $log = new Log();
                $log->description = 'delete Report';
                $log->value = $report;
                $log->type = 'ok';
                $report->log()->save($log);
                $report->delete();
                $log_text = 'Удалил отчет ' . $report->user->name . ' от ' . $report->created_at;
                (new CreateInfoLog($report->task))->text($log_text)->run();
                return response(true);
            }
            return response('', 405);
        }
        return response('', 404);
    }


}

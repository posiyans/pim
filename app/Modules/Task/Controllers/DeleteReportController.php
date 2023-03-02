<?php

namespace App\Modules\Task\Controllers;

use App\Http\Controllers\MyController;
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
//            if ($report->user_id == $user->id or $user->moderator) {
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
                return response();
            }
//            else {
//                $log = new Log();
//                $log->description = 'Access is denied';
//                $log->value = $report;
//                $log->type = 'alert';
//                $report->log()->save($log);
//            }
            return response('', 405);
        }
        return response('', 404);
    }


}

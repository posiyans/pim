<?php

namespace App\Modules\Task\Controllers;

use App\Http\Controllers\MyController;
use App\Modules\Log\Classes\CreateInfoLog;
use App\Modules\Task\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeleteReportController extends MyController
{

    public function index(Request $request)
    {
        $id = $request->id;
        $report = Report::find($id);
        $user = Auth::user();
        if ($report && ($user->moderator || $user->id === $report->user_id)) {
            if ($report->deleted_at) {
                return response(['status' => false, 'error' => 'Отчет уже удален']);
            }
            $report->delete();
            $log_text = 'Удалил отчет ' . $report->user->name . ' от ' . $report->created_at;
            (new CreateInfoLog($report->task))->text($log_text)->run();
            return response(true);
        }
        return response('', 404);
    }


}

<?php

namespace App\Modules\Task\Controllers;

use App\Http\Controllers\MyController;
use App\Modules\Task\Models\ViewReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SetReportAsReadController extends MyController
{

    public function index(Request $request)
    {
        $id = $request->id;
        $user = Auth::user();
        $report = ViewReport::where('task_id', $id)->where('user_id', $user->id)->first();
        if ($report) {
            $report->show = 0;
            $report->save();
        }
        return response([$report]);
    }


}

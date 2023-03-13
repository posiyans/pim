<?php

namespace App\Modules\Task\Controllers;

use App\Http\Controllers\MyController;
use App\Modules\Task\Models\ViewReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GetNoReadReportForUserController extends MyController
{

    public function index(Request $request)
    {
        $user = Auth::user();
        $report = ViewReport::where('user_id', $user->id)->get();
        $count = $report->sum('show');
        return response($count);
    }


}

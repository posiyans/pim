<?php

namespace App\Modules\File\Controlles;

use App\Http\Controllers\MyController;
use App\Modules\File\Models\File;
use App\Modules\Log\Classes\CreateInfoLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class DeleteFileController extends MyController
{

    public function __construct()
    {
        $this->middleware('only_moderator');
    }

    public function index(Request $request)
    {
        $uid = $request->uid;
        $file = File::where('uid', $uid)->first();
        if ($file) {
//            if (!$this->checkAccess($file)) {
//                return response('', 405);
//            }
            $model = $file->commentable;
            $file->delete();
            (new CreateInfoLog($model))->text('Удалил файл ' . $file->name)->run();
            return response(true);
        }
        return response('', 404);
    }


    private function checkAccess($file)
    {
        $user = Auth::user();
//        if ($user->moderator) {
//            return true;
//        }
        if ($file->commentable_type == 'App\Modules\Protocol\Models\Protocol') {
            return $user->moderator;
        }
//        if ($file->commentable_type == 'App\Modules\Task\Models\Report') {
//            $report = Report::find($file);
//            if ($report->user_id == $user->id) {
//                return true;
//            }
//            $executor = $report->task->executor->where('user_id', $user->id)->first();
//            if ($executor) {
//                return true;
//            }
//        }
        if ($file->commentable_type == 'App\Modules\Protocol\Models\Partition') {
            return $user->moderator;
        }
        return false;
    }

}

<?php

namespace App\Modules\File\Controlles;

use App\Http\Controllers\MyController;
use App\Modules\File\Models\File;
use App\Modules\File\Repositories\FileRepository;
use App\Modules\Task\Models\Report;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class DownloadFileController extends MyController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    public function index(Request $request)
    {
        $id = $request->id;
        $hash = $request->hash;
        $file = File::where('id', $id)->where('hash', $hash)->first();
        if ($file) {
            if (!$this->checkAccess($file)) {
                return response('', 405);
            }
            $path = FileRepository::getPathFromHash($file->hash);
            try {
                return response()->download($path, $file->name);
            } catch (\Exception $e) {
            }
        }
        return response('', 404);
    }


    private function checkAccess($file)
    {
        $user = Auth::user();
        if ($user->moderator) {
            return true;
        }
        if ($file->commentable_type == 'App\Modules\Protocol\Models\Protokol') {
            return $user->moderator;
        }
        if ($file->commentable_type == 'App\Modules\Task\Models\Report') {
            $report = Report::find($file);
            if ($report->user_id == $user->id) {
                return true;
            }
            $executor = $report->task->executor->where('user_id', $user->id)->first();
            if ($executor) {
                return true;
            }
        }
        if ($file->commentable_type == 'App\Modules\Protocol\Models\Partition') {
            return $user->moderator;
        }
        return false;
    }

}

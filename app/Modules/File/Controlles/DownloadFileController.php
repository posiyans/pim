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
use Illuminate\Support\Facades\Storage;


class DownloadFileController extends MyController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    public function index(Request $request)
    {
        $uid = $request->uid;
        $file = File::where('uid', $uid)->first();
        if ($file) {
            if (!$this->checkAccess($file)) {
                return response('', 405);
            }
            $path = FileRepository::getPathFromHash($file->hash);
            try {
                return Storage::download($path, $file->name);
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
        if ($file->commentable_type == 'App\Modules\Protocol\Models\Protocol') {
            return $user->moderator;
        }
        if ($file->commentable_type == 'App\Modules\Task\Models\Report') {
            $report = Report::find($file->commentable_id);
            if ($report->user_id == $user->id) {
                return true;
            }
            $executors = $report->task->getExecutor();
            foreach ($executors as $executor) {
                if ($executor->user->id == $user->id) {
                    return true;
                }
            }
        }
        if ($file->commentable_type == 'App\Modules\Protocol\Models\Partition') {
            return $user->moderator;
        }
        return false;
    }

}

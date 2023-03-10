<?php

namespace App\Modules\File\Controlles;

use App\Http\Controllers\MyController;
use App\Modules\File\Models\File;
use App\Modules\File\Repositories\FileRepository;
use App\Modules\Log\Classes\CreateInfoLog;
use App\Modules\Protocol\Models\Protocol;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class UploadFileController extends MyController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    public function index(Request $request)
    {
        try {
            $id = $request->id;
            $type = $request->type;
            $fileInput = $request->file('file');
            if ($fileInput && $type && $id) {
                if (!$this->checkAccess($type)) {
                    return response('', 405);
                }
                $class = $this->getClass($type);
                $model = $class::find($id);
                $md5 = md5_file($fileInput->getRealPath());
                $file = new File();
                $file->name = $fileInput->getClientOriginalName();
                $file->hash = $md5;
                $file->size = $fileInput->getSize();
                $model->files()->save($file);
                $path = FileRepository::getPathFromHash($md5, true);
                Storage::putFileAs($path, $fileInput, $md5);
                $text = 'Добавил файл ' . $file->name;
                (new CreateInfoLog($model))->text($text)->run();
                return response(true);
            }
        } catch (\Exception $e) {
            return response($e->getMessage(), 410);
        }
        return response('', 404);
    }

    private function getClass($type)
    {
        switch ($type) {
            case 'protocol':
                return Protocol::class;
            default:
                return throw new \Exception('Не известный тип модели');
        }
    }

    private function checkAccess($type)
    {
        $user = Auth::user();
        if ($user->moderator) {
            return true;
        }
        return false;
    }

}

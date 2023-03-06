<?php

namespace App\Modules\File\Controlles;

use App\Http\Controllers\MyController;
use App\Modules\File\Models\File;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;


class CovertFileController extends MyController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    public function index()
    {
        $replace = [
            'App\\Http\\Models\\Report' => 'App\\Modules\\Task\\Models\\Report',
            'App\\Http\\Models\\Protokol' => 'App\\Modules\\Protocol\\Models\\Protocol',
            'App\\Http\\Models\\Partition' => 'App\\Modules\\Protocol\\Models\\Partition',
        ];
        $files = File::all();
        foreach ($files as $file) {
            if (isset($replace[$file->commentable_type])) {
                $file->commentable_type = $replace[$file->commentable_type];
                $file->save();
            }
        }
    }


}

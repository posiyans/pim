<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;


class MyController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    public function md5_file($file)
    {
        $md5 = md5_file($file);
        $md5_sm = substr($md5, 0, 2);
        $folder = env('STORAGE_FILE_FOLDER', '');
        if (!file_exists($folder . '/' . $md5_sm)) {
            mkdir($folder . '/' . $md5_sm, 0777, true);
        }
        return ['md5' => $md5, 'folder' => $folder . '/' . $md5_sm];
    }

    public function save_file($file)
    {
        $md5 = $this->md5_file($file);
        if ($file->move($md5['folder'], $md5['md5'])) {
            return $md5['md5'];
        } else {
            return false;
        }
    }

}

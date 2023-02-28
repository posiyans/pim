<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;


class MyController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function response($var = [], $code = 200)
    {
        return response($var, $code)->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept, S-Token, U-Token ')->header(
            'Access-Control-Allow-Methods',
            'GET,HEAD,OPTIONS,POST,PUT'
        );
    }

    public function md5_file($file)
    {
        $md5 = md5_file($file);
        $md5_sm = substr($md5, 0, 2);
        $folder = config('filesystems.ppsd_file_folder');
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

<?php

namespace App\Modules\File\Repositories;


class FileRepository
{

    public static function getPathFromHash($hash)
    {
        $hash_sm = substr($hash, 0, 2);
        $folder = env('STORAGE_FILE_FOLDER', '');
        return $folder . '/' . $hash_sm . '/' . $hash;
    }

}
<?php

namespace App\Modules\File\Repositories;


class FileRepository
{

    public static function getPathFromHash($hash, $dir = false)
    {
        $hash_sm = substr($hash, 0, 2);
        $folder = 'hash';
        if ($dir) {
            return $folder . '/' . $hash_sm;
        }
        return $folder . '/' . $hash_sm . '/' . $hash;
    }

}
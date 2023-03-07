<?php

namespace App\Modules\Docx\Controllers;


use App\Http\Controllers\MyController;
use App\Modules\Docx\DocumentParser;
use Illuminate\Http\Request;

class ParseDocxController extends MyController
{

    public function index(Request $request)
    {
        $file = $request->file('file');
        $type = $file->getMimeType();
        $tmp_name = tempnam(sys_get_temp_dir(), 'php_docx');
        move_uploaded_file($file->getRealPath(), $tmp_name);
        $html = DocumentParser::parseFromFile($tmp_name, $type);
        $html = str_replace('</p>', '</p>' . PHP_EOL, $html);

//        return $html;
        return strip_tags($html, '');
    }
}

<?php

namespace App\Modules\Docx\Controllers;


use App\Http\Controllers\MyController;
use App\Modules\Docx\DocumentParser;
use App\Modules\File\Repositories\FileRepository;
use Illuminate\Http\Request;

class ParseDocxController extends MyController
{

    public function index(Request $request)
    {
        $file = $request->file('file');
        $type = $file->getMimeType();

        $hash = $this->save_file($file);
        $path = FileRepository::getPathFromHash($hash);
        $html = DocumentParser::parseFromFile($path, $type);
        $ar = [
            'rsidR="00DE185F"',
            'rsidRPr="00AC7075"',
            'rsidRDefault="00E616FD"',
            'rsidP="003D655D"',
            'rsidRDefault="00DE185F"',
            'rsidR="000E33E1"',
            'rsidP="000E33E1"',
            'rsidRDefault="00DC7595"',


        ];
//        $html = str_replace($ar, '', $html);
        $html = str_replace('</p>', '</p>' . PHP_EOL, $html);

//        return $html;
        return strip_tags($html, '');
    }
}

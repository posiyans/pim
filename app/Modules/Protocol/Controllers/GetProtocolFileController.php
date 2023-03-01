<?php

namespace App\Modules\Protocol\Controllers;

use App\Http\Controllers\MyController;
use App\Modules\File\Repositories\FileRepository;
use App\Modules\Protocol\Models\Protokol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

class GetProtocolFileController extends MyController
{


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $id = $request->id;
        if ($user->moderator) {
            $item = Protokol::find($id);
            if ($item && count($item->file) > 0) {
                $file = $item->file;
                $md5 = $file[0]->hash;
                $name = $file[0]->name;
                $path = FileRepository::getPathFromHash($md5);
                return response()->download($path, $name, $headers = ['filename' => $name]);
            }
            return response('Файл не найден', 404);
        }
    }


}

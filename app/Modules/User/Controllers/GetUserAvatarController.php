<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\MyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GetUserAvatarController extends MyController
{

    public function index(Request $request)
    {
        $id = $request->id;
        $file = Storage::path('avatar/avatar_' . $id . '.jpg');
//            $path = FileRepository::getPathFromHash($hash);
        if (file_exists($file)) {
            return response()->download($file, 'avatar.jpg');
        }
        return response()->download(__DIR__ . '/' . 'user.jpg', 'user.jpg');
    }

}

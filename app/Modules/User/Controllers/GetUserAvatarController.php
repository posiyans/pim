<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\MyController;
use App\Models\User;
use Illuminate\Http\Request;

class GetUserAvatarController extends MyController
{

    public function index(Request $request)
    {
        $id = $request->id;
        $user = User::find($id);
        $hash = $user->avatar;
        if ($hash) {
            $path = FileRepository::getPathFromHash($hash);
            if (file_exists($path)) {
                return response()->download($path, 'avatar.jpg');
            }
        }
        return response()->download(__DIR__ . '/' . 'user.jpg', 'user.jpg');
    }

}

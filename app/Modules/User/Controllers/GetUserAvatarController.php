<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\MyController;
use App\Models\User;
use App\Modules\File\Classes\GravatarClass;
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
        $user = User::find($id);
        if ($user) {
            $label = mb_strtolower(trim(mb_substr($user->name, 0, 2)));
            $generator = new GravatarClass();
            $avatar = $generator->string($label)->toPng();
            return response($avatar, 200, [
                'Content-Type' => 'image/png',
                'Content-Disposition' => 'attachment; filename="avatar.png"',
            ]);
        }
        return response()->download(__DIR__ . '/' . 'user.jpg', 'user.jpg');
    }

}

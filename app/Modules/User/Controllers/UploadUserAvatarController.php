<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\MyController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UploadUserAvatarController extends MyController
{

    public function index(Request $request)
    {
        $avtor = Auth::user();
        if ($avtor->moderator) {
            $id = $request->id;

            $user = User::find($id);
            if ($user) {
                $file = $request->file('avatar');
                $store = 'avatar';
                $name = $file->getClientOriginalName();
                $file->storeAs($store, 'avatar_' . $user->id . '.jpg');
                $user->avatar = $name;
                $user->save();
                //$name = $user->avatar;
                $path = '../storage/file/avatar/avatar_' . $user->id . '.jpg';
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                return response(['files' => ['avatar' => $base64]]);
            } else {
                return response(['error'], 500);
            }
        }
        return response()->download(__DIR__ . '/' . 'user.jpg', 'user.jpg');
    }

}

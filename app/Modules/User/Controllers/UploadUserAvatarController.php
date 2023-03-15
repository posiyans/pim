<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\MyController;
use App\Models\User;
use Illuminate\Http\Request;

class UploadUserAvatarController extends MyController
{
    public function __construct()
    {
        $this->middleware('only_moderator');
    }

    public function index(Request $request)
    {
        $id = $request->id;
        $user = User::find($id);
        if ($user) {
            $file = $request->file('avatar');
            $store = 'avatar';
            $file->storeAs($store, 'avatar_' . $user->id . '.jpg');
            return response(true);
        } else {
            return response(['error'], 500);
        }
    }

}

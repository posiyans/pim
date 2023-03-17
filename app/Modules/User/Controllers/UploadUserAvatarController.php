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
        $autor = Auth::user();
        if ($autor->moderator) {
            $id = $request->id;
        } else {
            $id = Auth::id();
        }
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

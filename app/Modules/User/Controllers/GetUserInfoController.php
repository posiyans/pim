<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\MyController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GetUserInfoController extends MyController
{
    //
    public function index(Request $request)
    {
        $avtor = Auth::user();
        $id = $avtor->id;
        if ($avtor->moderator) {
            $id = $request->id;
        }
        $user = User::find($id);
        return response($user);
    }


}

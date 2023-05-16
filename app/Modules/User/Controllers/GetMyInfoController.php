<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\MyController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class GetMyInfoController extends MyController
{

    public function index()
    {
        $user = Auth::user();
        if ($user) {
            $user->roles = array_values($user->roles);
            return response($user);
        }
        $check = User::first();
        if ($check) {
            return response(['message' => 'Unauthenticated.'], 401);
        }
        return response(['message' => 'Create new user'], 410);
    }

}

<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\MyController;
use Illuminate\Support\Facades\Auth;

class GetMyInfoController extends MyController
{

    public function index()
    {
        $user = Auth::user();
        $user->roles = array_values($user->roles);
        return response($user);
    }

}

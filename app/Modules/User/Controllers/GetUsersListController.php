<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\MyController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GetUsersListController extends MyController
{
    //
    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user->moderator) {
            $limit = $request->limit;
            $users = User::query()->paginate($limit);
            return response($users);
        }


        return response('', 405);
    }


}

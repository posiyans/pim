<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\MyController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GetUsersListController extends MyController
{
    public function __construct()
    {
        $this->middleware('only_moderator');
    }

    //
    public function index(Request $request)
    {
        $limit = $request->limit;
        $user = Auth::user();
        $query = User::query();
        if (!in_array('admin', $user->roles)) {
            $query->where('roles', 'not like', '%admin%')->orWhere('hide', false);
        }
        $users = $query->paginate($limit);
        return response($users);
    }


}

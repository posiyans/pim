<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\MyController;
use App\Models\User;
use Illuminate\Http\Request;

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
        $users = User::query()->paginate($limit);
        return response($users);
    }


}

<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\MyController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GetUsersExecutorsController extends MyController
{
    //
    public function index(Request $request)
    {
        $fields = [];
        $fields['id'] = 'id';
        $fields['name'] = 'name';
        $fields['full_name'] = 'full_name';
//        $fields['hide'] = 'hide';
        $users = User::all();
        $data = [];
        $avtor = Auth::user();
        foreach ($users as $user) {
            $item = [];
            foreach ($fields as $key => $value) {
                $item[$key] = $user->{$value};
            }
            $item['hide'] = true;
            if ($avtor->moderator) {
                $item['hide'] = $user->hide;
            } else {
                if (!$user->hide) {
                    if ($avtor->id == $user->id) {
                        $item['hide'] = false;
                    }
                    if (isset($avtor->aliases[$user->id])) {
                        $item['hide'] = false;
                    }
                }
            }
            $data[] = $item;
        }
        return response($data);
    }


}

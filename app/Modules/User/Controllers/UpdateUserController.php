<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\MyController;
use App\Models\User;
use Illuminate\Http\Request;

class UpdateUserController extends MyController
{
    public function __construct()
    {
        $this->middleware('only_moderator');
    }

    public function index(Request $request)
    {
        $edit = false;
        if ($request->id) {
            $user = User::find($request->id);
            $edit = true;
        } else {
            $user = new User();
        }
        if ($user) {
            $user_old = clone $user;
            $user->name = $request->name ?? $user->name;
            $user->full_name = $request->full_name ?? $user->full_name;
            $user->login = $request->login ?? $user->login;
            $opt = $user->options;
            $opt['phone'] = $request->phone ?? $opt['phone'] ?? '';
            $opt['color'] = $request->color ?? $opt['color'] ?? '';
            $opt['telegram'] = $request->telegram ?? $opt['telegram'] ?? '';
            $user->options = $opt;
//                $user->login_by_sms = $request->login_by_sms ?? $user->login_by_sms;
            $user->aliases = $request->aliases ?? $user->aliases;
            if ($request->has('hide')) {
                $user->hide = $request->hide ?? null;
            }
            $user->moderator = $request->moderator ?? $user->moderator;
            if ($user->save()) {
//                    if ($edit) {
//                        Log::saveDiff($user, $user_old);
//                    }
            }

            return response('');
        }
        return response('', 404);
    }


}

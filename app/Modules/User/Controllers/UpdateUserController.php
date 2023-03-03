<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\MyController;
use App\Models\User;
use App\Modules\Log\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpdateUserController extends MyController
{
    //
    public function index(Request $request)
    {
        $moderator = Auth::user();
        $edit = false;
        if ($moderator->moderator) {
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
                $user->phone = $request->phone ?? $user->phone;
                $user->login_by_sms = $request->login_by_sms ?? $user->login_by_sms;
                $user->aliases = $request->aliases ?? $user->aliases;
                $user->hide = $request->hide ?? $user->hide;
                if ($user->save()) {
                    if ($edit) {
                        Log::saveDiff($user, $user_old);
                    }
                }

                return response('');
            }
            return response('', 404);
        }
        return response('', 405);
    }


}

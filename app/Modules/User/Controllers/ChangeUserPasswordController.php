<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\MyController;
use App\Models\User;
use App\Modules\Log\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ChangeUserPasswordController extends MyController
{

    public function __construct()
    {
        $this->middleware('only_moderator');
    }

    public function index(Request $request)
    {
        $password = $request->password;
        $id = $request->id;
        $user = User::find($id);
        if (strlen($password < 8)) {
            return response('Пароль должен быть от 8 символов', 405);
        }
        if ($user) {
            $user->password = Hash::make($password);
            $log = new Log();
            $log->description = 'Смена пароля';
            $log->type = 'ok';
            $user->log()->save($log);
            $user->save();
            return response('');
        }
        return response('', 404);
    }

}

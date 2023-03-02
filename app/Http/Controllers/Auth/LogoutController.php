<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\MyController;
use App\Modules\Log\Models\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;


class LogoutController extends MyController
{

    public function index()
    {
        $user = Auth::user();
        $tokenId = Str::before(request()->bearerToken(), '|');
        if ($tokenId) {
            $user->tokens()->where('id', $tokenId)->delete();
        }
        $log = new Log();
        $log->type = 'ok';
        $log->description = 'logout';
        $user->log()->save($log);
        return true;
    }


}

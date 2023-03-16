<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\MyController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpdateUserTwoFactorSettingController extends MyController
{
    //
    public function index(Request $request)
    {
        $avtor = Auth::user();
        $id = $avtor->id;
        if ($avtor->moderator && $request->id) {
            $id = $request->id;
        }
        $user = User::find($id);
        $user->two_factor = $request->two_factor ?? $user->two_factor;
        $opt = $user->options;
        $opt['two_factor_enable'] = $request->two_factor_enable ?? $opt['two_factor_enable'] ?? [];
        $user->options = $opt;
        $user->save();
        //todo add log
        return response(true);
    }


}

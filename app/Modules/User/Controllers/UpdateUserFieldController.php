<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\MyController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpdateUserFieldController extends MyController
{

    public function index(Request $request)
    {
        $access_field = [
            'email',
            'telegram',
        ];
        $field = $request->get('field', false);
        $user_id = $request->get('user_id', false);
        $author = Auth::user();
        if (in_array($field, $access_field)) {
            if ($author->moderator && $user_id) {
                $user = User::find($user_id);
            } else {
                $user = User::find($author->id);
            }
            switch ($field) {
                case 'email':
                    $validated = $request->validate([
                        'value' => 'email|nullable'
                    ]);
                    $user->email = $validated['value'];
                    break;
                case 'telegram':
                    $validated = $request->validate([
                        'value' => 'numeric|nullable'
                    ]);
                    $opt = $user->options;
                    $opt['telegram'] = $validated['value'];
                    $user->options = $opt;
                    break;
            }
            $user->save();
        }
        return response('');
    }


}

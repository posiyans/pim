<?php

namespace App\Models\Ppsd;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class UserPpsd extends Model
{
    protected $connection = 'mysql_ppsd';
    protected $table = 'users';
    public $timestamps = false;

    public static function userMigrate()
    {
        if (count(User::all())> 0) {
            dump('User no migrate');
        }else{
            $userPpsd = UserPpsd::all();
            foreach ($userPpsd as $item) {
                $user = new User();
                $user->id = $item->user_id;
                $user->login = $item->user_login;
                $user->name = $item->name;
                $user->full_name = $item->full_name;
                $user->moderator = $item->moderator;
                $user->last_connect = $item->last_conect;
                $user->phone = $item->phone;
                $user->color = $item->color;
                $user->hide = $item->hide;
                $user->telegram = $item->telegram;
                if (isset($item->dostup)) {
                    $user->aliases = explode(",", $item->dostup);
                }else{
                    $user->aliases = [];
                }
                $user->password = Hash::make($item->user_password);
                $user->save();
                $user->syncRoles(['user']);
                if ($user->id == 15){
                    $user->syncRoles(['user', 'admin', 'SuperAdmin']);
                }
            }
            dump('User ok');
        }


    }

}

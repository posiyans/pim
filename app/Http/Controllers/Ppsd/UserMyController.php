<?php

namespace App\Http\Controllers\Ppsd;

use App\Http\Controllers\Sms\Sms;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class UserMyController extends Controller
{
    //

    /**
     * вывод списка пользователя по полученному шаблону
     *
     * @param Request $request
     * @return mixed
     */
    public function getList(Request $request)
    {
        $data = [];
        $fields = [];
        if ($request->field) {
            $list = $request->field;
            $list = str_replace('"', '', $list);
            $list = str_replace('{', '', $list);
            $list = str_replace('}', '', $list);
            foreach (explode(',', $list) as $value) {
                $i = explode(':', $value);
                $fields[$i[0]] = $i[1];
            }
        } else {
            $fields['name'] = 'name';
            $fields['login'] = 'login';
            $fields['full_name'] = 'full_name';
            $fields['email'] = 'email';
            $fields['avatar'] = 'avatar';
        }
        $user = Auth::user();
        if (!$user->hasRole('admin')) {
            $executor = $user->aliases;
            array_push($executor, $user->id);
            $users = User::where('hide', null)->whereIn('id', $executor)->get();
        } else {
            $users = User::where('hide', null)->get();
        }

        foreach ($users as $user) {
            $item = [];
            foreach ($fields as $key => $value) {
                $item[$key] = $user->{$value};
            }
            $data[] = $item;
        }
        return $this->response(['user' => $data]);
    }

    public function show($id = false, Request $request)
    {
        if ($id) {
            $user = User::find($id);
        } else {
            $user = Auth::user();
        }
        if (Auth::user()->hasRole('admin')) {
            $apikey = env('SMS_API_KEY');
            $smsru = new Sms($apikey);
            $request = $smsru->getBalance();
            if ($request->status == "OK") {
                $user->sms = $request->balance;
            } else {
                $user->sms = '';
            }
        } else {
            $user->sms = '';
        }

        if ($user) {
            $user->roles_json = $user->getRoleNames()->toArray();
            return $this->response($user);
        } else {
            return $this->response(['error']);
        }
    }

    /**
     * Оutput image avatar.
     *
     * @return \Illuminate\Http\Response
     */
    public function userAvatar($id = false, Request $request)
    {
        if (Auth::check()) {
            // download avatar
            if ($request->method() == 'GET') {
                if ($id) {
                    $user = User::find($id);
                } else {
                    $user = Auth::user();
                }
                $path = false;
                if ($user->avatar) {
                    //$name = $user->avatar;
                    $path = '../storage/app/file/avatar/' . $user->id;
                }
                if (!file_exists($path)) {
                    $path = '../storage/app/file/avatar/logo.jpg';
                    //$name = 'logo.jpg';
                }
                return response()->download($path, $user->avatar);
            } else {
                // upload avatar
                if ($id) {
                    $user = User::find($id);
                    $file = $request->file('avatar');
                    $store = 'file/avatar';
                    $name = $file->getClientOriginalName();
                    $file->storeAs($store, $id);
                    $user->avatar = $name;
                    $user->save();
                    //$name = $user->avatar;
                    $path = '../storage/app/file/avatar/' . $user->id;
                    $type = pathinfo($path, PATHINFO_EXTENSION);
                    $data = file_get_contents($path);
                    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                    return $this->response(['files' => ['avatar' => $base64]]);
                    // return response()->download($path,$user->avatar);
                } else {
                    return $this->response(['error'], 500);
                }
            }
        }
    }

    /**
     * Display a listing of the resource.
     *
     * Вывод списка пользователей
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $limit = (int)$request->limit;
        if (Auth::user()->assignRole('SuperAdmin')) {
            $users = User::with('roles')->paginate($limit);
        } else {
            $users = User::role(Auth::user()->with('roles')->getRoleNames())->paginate($limit);
        }
        $total = $users->total();
        $users->each(function ($user) {
            $user->roles_json = str_replace(',', ", ", $user->getRoleNames()->toArray());
        });
        return $this->response(['total' => $total, 'items' => $users]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $resp = false;
        $user = User::find($id);
        if ($request->has('user')) {
            $data = $request->user;
            if ($user->id == $data['id']) {
                $user->color = $data['color'];
                $user->email = $data['email'];
                $user->login = $data['login'];
                $user->name = $data['name'];
                $user->full_name = $data['full_name'];
                $user->login_by_sms = $data['login_by_sms'];
                $user->phone = $data['phone'];
                $user->telegram = $data['telegram'];
                $user->syncRoles($data['roles_json']);
                //$user->roles = $data['roles'];
                $user->aliases = $data['aliases'];
                $resp = $user->save();
            }
        }
        if ($request->has('password')) {
            $data = $request->password;
            if ($user->id == $data['id']) {
                $user->password = Hash::make($data['password']);
                $resp = $user->save();
            }
        }
        if ($resp) {
            return $this->response(['ok']);
        } else {
            return $this->response(400, ['error']);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->hasRole('admin')) {
            $data = $request->user;
            $newUser = new User();
            $newUser->color = $data['color'];
            $newUser->email = $data['email'];
            $newUser->login = $data['login'];
            $newUser->name = $data['name'];
            $newUser->full_name = $data['full_name'];
            $newUser->login_by_sms = $data['login_by_sms'];
            $newUser->phone = $data['phone'];
            $newUser->telegram = $data['telegram'];
            $newUser->roles = $data['roles'];
            $newUser->aliases = $data['aliases'];
            $newUser->save();
            return $this->response(['ok', $newUser]);
        }
        return $this->response(['error'], 403);
    }

}

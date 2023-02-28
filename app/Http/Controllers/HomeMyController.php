<?php

namespace App\Http\Controllers;

use App\Http\Models\UserPpsd;

class HomeMyController extends MyController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

//    /**
//     * Show the application dashboard.
//     *
//     * @return \Illuminate\Contracts\Support\Renderable
//     */
//    public function index()
//    {
//    }

}

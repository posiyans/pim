<?php

namespace App\Modules\File\Models;

use App\MyModel;
use Illuminate\Support\Facades\Auth;

class File extends MyModel
{
    //
    public function commentable()
    {
        return $this->morphTo();
    }

    /**
     * File constructor.
     */
    public function __construct()
    {
        if (Auth::check()) {
            $this->user_id = Auth::user()->id;
        }
    }
}

<?php

namespace App\Models;

use App\MyModel;
use Illuminate\Database\Eloquent\SoftDeletes;


class Report extends MyModel
{
    use SoftDeletes;

    //
    protected $dates = ['deleted_at'];

    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
}


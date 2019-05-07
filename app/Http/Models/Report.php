<?php

namespace App\Http\Models;

use App\MyModel;
use Illuminate\Database\Eloquent\SoftDeletes;


class Report extends MyModel
{
    use SoftDeletes;
    //
    protected $dates = ['deleted_at'];

    public function user()
    {
        return $this->hasOne('App\User','id', 'user_id');
    }
}


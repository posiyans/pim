<?php

namespace App\Modules\Task\Models;

use App\MyModel;
use Illuminate\Database\Eloquent\SoftDeletes;


class Report extends MyModel
{
    use SoftDeletes;

    //
//    protected $dates = ['deleted_at'];

    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function task()
    {
        return $this->hasOne('App\Modules\Task\Models\Task', 'id', 'task_id');
    }

}


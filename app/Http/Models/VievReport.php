<?php

namespace App\Http\Models;

use App\MyModel;

class VievReport extends MyModel
{
    //

    public function user()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }
    public function task()
    {
        return $this->hasOne('App\Http\Models\Task', 'id', 'task_id');
    }
}

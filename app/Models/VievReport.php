<?php

namespace App\Models;

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
        return $this->hasOne('App\Models\Task', 'id', 'task_id');
    }
}

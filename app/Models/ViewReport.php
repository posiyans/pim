<?php

namespace App\Models;

use App\MyModel;

class ViewReport extends MyModel
{
    //
    protected $table = 'viev_reports';

    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function task()
    {
        return $this->hasOne('App\Modules\Task\Models\Task', 'id', 'task_id');
    }
}

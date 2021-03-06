<?php

namespace App\Http\Models;

use App\MyModel;

class Partition extends MyModel
{
    //
    public function task()
    {
        return $this->hasMany('App\Http\Models\Task', 'partition_id', 'id');
    }

    public function protokol(){
        return $this->belongsTo('App\Http\Models\Protokol');
    }

    public function getPercentComplete(){
        $PercentComplete = [];
        foreach ($this->task as $task) {
            $PercentComplete[]=$task->getPercentComplete();
        }
        if (count($PercentComplete) != 0){
            $result = round(array_sum($PercentComplete)/count($PercentComplete), 0);
        }else{
            $result = 0;
        }

        return $result;
    }

}

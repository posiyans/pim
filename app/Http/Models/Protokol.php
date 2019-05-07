<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Collection;
use App\MyModel;
use App\Http\Models\Partition;

class Protokol extends MyModel
{
    //
    protected $casts = [
        'descriptions' => 'array',
    ];


    public function partition()
    {
        return $this->hasMany('App\Http\Models\Partition', 'protokol_id', 'id');
    }


    /**
     * Получить log protokol
     */
    public function log()
    {
        return $this->morphMany('App\Http\Models\Log', 'commentable');

    }

    public function partitionAndTask()
    {
        $partitions = $this->partition;
        foreach ($partitions as $partition){
            $partition->task;
        }
        return $partitions;
    }
    public function task()
    {
        $partitions = $this->partition;
        $task = new Collection;
        foreach ($partitions as $partition){
            $task = $task->merge($partition->task);
        }
        return $task;
    }
    public function getPercentComplete()
    {
        $PercentComplete = [];
        foreach ($this->partition as $partition){
            $PercentComplete[]=$partition->getPercentComplete();
        }
        if (count($PercentComplete) == 0){
            return 0;
        } else {
            return round(array_sum($PercentComplete) / count($PercentComplete), 0);
        }
    }

}

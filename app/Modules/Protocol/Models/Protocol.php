<?php

namespace App\Modules\Protocol\Models;

use App\MyModel;
use Illuminate\Database\Eloquent\Collection;

class Protocol extends MyModel
{

    //
    protected $casts = [
        'descriptions' => 'array',
    ];


    public function partition()
    {
        return $this->hasMany('App\Modules\Protocol\Models\Partition', 'protocol_id', 'id');
    }


    /**
     * Получить log protokol
     */
    public function log()
    {
        return $this->morphMany('App\Modules\Log\Models\Log', 'commentable');
    }

    public function partitionAndTask()
    {
        $partitions = $this->partition;
        foreach ($partitions as $partition) {
            $partition->tasks;
        }
        return $partitions;
    }

    /**
     * @return Collection
     * @deprecated
     *
     */
    public function task()
    {
        return $this->tasks();
    }

    public function tasks()
    {
        $partitions = $this->partition;
        $task = new Collection;
        foreach ($partitions as $partition) {
            $task = $task->merge($partition->tasks);
        }
        return $task;
    }

    public function getPercentComplete()
    {
        $PercentComplete = [];
        foreach ($this->partition as $partition) {
            $PercentComplete[] = $partition->getPercentComplete();
        }
        if (count($PercentComplete) == 0) {
            return 0;
        } else {
            return round(array_sum($PercentComplete) / count($PercentComplete), 0);
        }
    }

}

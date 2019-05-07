<?php

namespace App\Http\Models;

use App\MyModel;
use Carbon\Carbon;

class Task extends MyModel
{
    //
    public $remove = false;

    public function report()
    {
        if ($this->remove){
            $this->remove = false;
            return $this->hasMany('App\Http\Models\Report')->withTrashed();
        }else{
            return $this->hasMany('App\Http\Models\Report');
        }
    }

    public function partition()
    {
        return $this->hasOne('App\Http\Models\Partition', 'id', 'partition_id');
    }

    public function protokol()
    {
        return $this->hasOne('App\Http\Models\Protokol', 'id', 'protokol_id');
    }

    public function viewReport()
    {
        return $this->hasMany('App\Http\Models\VievReport', 'task_id', 'id');
    }

//    public function getDataIspolnAttribute($value)
//    {
//        if ($value) {
//            return Carbon::createFromFormat('Y-m-d', $value)->format('d-m-Y');
//        }
//    }

    public function getExecutor(){
        $executors = VievReport::where('executor', 1)->where('task_id',$this->id)->get();
        foreach ($executors as $executor){
            $executor->user;
        }
        return $executors;
    }

    public function reportAndUser()
    {
        foreach ($this->report as $report){
            $report->user;
            $report->file;
        }
        return $this;
    }

    public function allReportAndUser()
    {
        $this->remove = true;
        foreach ($this->report as $report){
            $report->user;
            $report->file;
        }
        return $this;
    }

    public function getPercentComplete(){
        $complete = 0;
        $reports = $this->viewReport;
        if ($reports) {
            $total_work = $reports->where('executor', 1)->count();
            $in_work = $reports->where('executor', 1)->where('done', null)->count();
            if ($total_work != 0){
                $complete = round(100 * ($total_work - $in_work) / $total_work, 0);
            }
        }
        return $complete;
    }

}

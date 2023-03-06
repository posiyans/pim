<?php

namespace App\Modules\Task\Models;

use App\Models\ViewReport;
use App\MyModel;
use Illuminate\Support\Facades\Auth;

class Task extends MyModel
{
    //
    public $remove = false;

    /**
     * отношения с отчетами текущие и удаленные
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function report()
    {
        if ($this->remove) {
            $this->remove = false;
            return $this->hasMany('App\Modules\Task\Models\Report')->withTrashed();
        } else {
            return $this->hasMany('App\Modules\Task\Models\Report');
        }
    }

    /**
     * отношения с разделами
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function partition()
    {
        return $this->hasOne('App\Modules\Protocol\Models\Partition', 'id', 'partition_id');
    }

    /**
     * отношения с протоколами
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function protokol()
    {
        return $this->hasOne('App\Modules\Protocol\Models\Protokol', 'id', 'protokol_id');
    }

    /**
     * отношения с просмотром отчетов
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function viewReport()
    {
        return $this->hasMany('App\Models\ViewReport', 'task_id', 'id');
    }

    /**
     * возвращает исполнителей для задачи
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getExecutor()
    {
        $executors = ViewReport::where('executor', 1)->where('task_id', $this->id)->get();
        foreach ($executors as $executor) {
            $executor->user;
        }
        return $executors;
    }

    /**
     * добавляет к задаче отченты
     * @return $this
     */
    public function reportAndUser()
    {
        foreach ($this->report as $report) {
            $report->user;
            $report->file;
        }
        return $this;
    }

    /**
     * добавляет к задаче отчеты и удаленные отчеты
     * @return $this
     */
    public function allReportAndUser()
    {
        $this->remove = true;
        foreach ($this->report as $report) {
            $report->user;
            $report->file;
        }
        return $this;
    }

    /**
     * возврящает процент выполнения задачи
     * @return float|int
     */
    public function getPercentComplete()
    {
        $complete = 0;
        $reports = $this->viewReport;
        if ($reports) {
            $total_work = $reports->where('executor', 1)->count();
            $in_work = $reports->where('executor', 1)->where('done', null)->count();
            if ($total_work != 0) {
                $complete = round(100 * ($total_work - $in_work) / $total_work, 0);
            }
        }
        return $complete;
    }

    /**
     * проверка доступа к задаче
     * @return bool
     */
    public function isAccess()
    {
        $access = false;
        $user = Auth::user();
        if ($user->moderator) {
            $access = true;
        }
        $executor = $user->aliases;
        array_push($executor, $user->id);
        $task = ViewReport::where('executor', 1)->whereIn('user_id', $executor)->where('task_id', $this->id)->pluck('task_id')->toArray();
        if (count($task) > 0) {
            $access = true;
        }
        return $access;
    }
}

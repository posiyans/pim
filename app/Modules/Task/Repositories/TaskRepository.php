<?php


namespace App\Modules\Task\Repositories;

use App\Modules\Task\Models\Report;
use App\Modules\Task\Models\Task;
use App\Modules\Task\Models\ViewReport;

class TaskRepository
{
    private $query;
    private $executors;
    private $status;

    public function __construct()
    {
        $this->status = null;
        $this->executors = null;
        $this->query = Task::query();
    }

    public function status($status)
    {
        $this->status = $status;
//            value: 1,     label: 'Закрытые'
//            value: 2,      label: 'Не закрытые'
//            value: 3,    label: 'Просроченные'
//            value: 4,        label: 'Без ответа'
//        $this->query->where('executor', 1)->whereNotNull('done')->count();
        return $this;
    }

    public function find($find)
    {
        $find = explode(' ', $find);
        foreach ($find as $value) {
            $this->query->whereRaw("(lower(concat_ws(' ',id, text))) like '%" . strtolower($value) . "%'");
        }
        return $this;
    }

    public function today()
    {
        $this->query->where(function ($query) {
            $today = date('Y-m-d');
            $query->where('data_ispoln', $today)->orWhere('data_perenosa', $today);
        });
        return $this;
    }

    public function executors($executors)
    {
//        if (is_array($this->executors)) {
//            $this->executors = array_intersect($this->executors, $executors);
//        } else {
        $this->executors = $executors;
//        }
//        $this->query->whereIn('id', ViewReport::select(['task_id'])->where('executor', 1)->whereIn('user_id', $executors));
        return $this;
    }

    public function archiv($archiv)
    {
        if ($archiv) {
            $this->query->whereNotNull('arxiv');
        } else {
            $this->query->whereNull('arxiv');
        }
        return $this;
    }


    public function paginate($limit)
    {
        if ($this->executors) {
            $this->query->whereIn('id', ViewReport::select(['task_id'])->where('executor', 1)->whereIn('user_id', $this->executors));
        }
        if ($this->status) {
            switch ($this->status) {
                case 1:
                    if ($this->executors) {
                        $this->query->whereIn('id', ViewReport::select(['task_id'])->where('executor', 1)->whereNotNull('done')->whereIn('user_id', $this->executors));
                    } else {
                        $this->query->whereNotIn('id', ViewReport::select(['task_id'])->where('executor', 1)->whereNull('done'));
                    }
                    break;
                case 2:
                    if ($this->executors) {
                        $this->query->whereIn('id', ViewReport::select(['task_id'])->where('executor', 1)->whereNull('done')->whereIn('user_id', $this->executors));
                    } else {
                        $this->query->whereIn('id', ViewReport::select(['task_id'])->where('executor', 1)->whereNull('done'));
                    }
                    break;
                case 3:
                    if ($this->executors) {
                        $this->query->whereIn('id', ViewReport::select(['task_id'])->where('executor', 1)->whereNull('done')->whereIn('user_id', $this->executors));
                    } else {
                        $this->query->whereIn('id', ViewReport::select(['task_id'])->where('executor', 1)->whereNull('done'));
                    }
                    $this->query->where(function ($query) {
                        $today = date('Y-m-d');
                        $query->where('data_ispoln', '<', $today)
                            ->orWhere('data_perenosa', '<', $today);
                    });
                    break;
                case 4:
                    if ($this->executors) {
                        $this->query->whereIn('id', ViewReport::select(['task_id'])->where('executor', 1)->whereNull('done')->whereIn('user_id', $this->executors));
                        $this->query->whereNotIn('id', Report::select(['task_id'])->whereIn('user_id', $this->executors));
                    } else {
                        $this->query->whereIn('id', ViewReport::select(['task_id'])->where('executor', 1)->whereNull('done'));
                        $this->query->whereNotIn('id', Report::select(['task_id']));
                    }

                    break;
            }
        }
        $this->query->orderBy('id', 'desc');
        return $this->query->paginate($limit);
    }

    public function all()
    {
    }

}

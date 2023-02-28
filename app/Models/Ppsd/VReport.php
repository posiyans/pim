<?php

namespace App\Models\Ppsd;

use App\Models\Task;
use App\Models\VievReport;
use Illuminate\Database\Eloquent\Model;

class VReport extends Model
{
    //
    protected $connection = 'mysql_ppsd';
    protected $table = 'contorl_zadach';
    public $timestamps = false;

    public static function VreportMigrate()
    {

        if (count(VievReport::all()) > 0) {
            dump('Report no migrate');
        } else {

            foreach (Task::all() as $task) {
                $countReport[$task->id] = $task->report->count();
            }
            //dd($countReport);

            $i = 0;
            $report = VReport::all();
            foreach ($report as $item) {
                if (isset($countReport[$item->zadach]) and $item->user != 14 and $item->user != 16) {
                    $r = new VievReport();
                    $r->task_id = $item->zadach;
                    $r->user_id = $item->user;
                    $r->executor = 1;
                    $r->done = $item->potvergdenie;
                    $r->show = $countReport[$item->zadach] - $item->show;
                    $r->created_at = $item->time;
                    $r->save();
                    $i++;
                }
            }

            $report = \App\Models\Ppsd\VmReport::all();
            foreach ($report as $item) {
                if (isset($countReport[$item->zadach]) and $item->user != 14 and $item->user != 16) {
                    $r = new VievReport();
                    $r->task_id = $item->zadach;
                    $r->user_id = $item->user;
                    $r->executor = 0;
                    $r->done = $item->potvergdenie;
                    $r->show = $countReport[$item->zadach] - $item->show;
                    $r->created_at = $item->time;
                    $r->save();
                    $i++;
                }
            }
            dump('View count:'.$i);
        }


    }


}

<?php

namespace App\Models\Ppsd;

use App\Modules\Protocol\Models\Partition;
use App\Modules\Protocol\Models\Protokol;
use App\Modules\Task\Models\Task;
use Illuminate\Database\Eloquent\Model;

class TaskPpsd extends Model
{
    //
    protected $connection = 'mysql_ppsd';
    protected $table = 'zadach';
    public $timestamps = false;


    public static function TaskMigrate()
    {
        if (count(Task::all()) > 0) {
            dump('Task no migrate');
        } else {
            foreach (Protokol::all() as $item) {
                if (isset($pr[$item['nomer']])) {
                    dump($item['nomer']);
                }
                $pr[$item['nomer']] = $item['id'];
            }
            $taskPpsd = TaskPpsd::all();
            $i = 0;
            foreach ($taskPpsd as $item) {
                $task = new Task();
                $task->id = $item->id;
                $t = $item->nomer;
                $partition = Partition::where('protokol_id', $pr[$item->protokol])->where('number', (int)$item->nomer)->first();
                if ($partition) {
                    $task->partition_id = $partition->id;
                } else {
                    $task->partition_id = 1;
                }
                $task->number = $item->nomer;
                if ($item->data_ispoln != '0000-00-00') {
                    $task->data_ispoln = $item->data_ispoln;
                }
                $task->data_perenosa = $item->data_perenosa;
                $task->text = $item->text;
                $task->autor_id = $item->avtor;
                $task->executor = $item->ispolnitel;
                $task->protokol_id = $pr[$item->protokol];
                $task->history = $item->hist;
                $task->full_history = $item->hide_hist;
                $task->arxiv = $item->arxiv;
                $task->save();
                $i++;
                //dump($task);
            }
            dump('Task count:' . $i);
        }
    }
}

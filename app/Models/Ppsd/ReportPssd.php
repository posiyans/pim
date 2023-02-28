<?php

namespace App\Models\Ppsd;

use App\Models\File;
use App\Models\Report;
use Illuminate\Database\Eloquent\Model;

class ReportPssd extends Model
{
    //
    protected $connection = 'mysql_ppsd';
    protected $table = 'prim';
    public $timestamps = false;

    public static function TaskMigrate()
    {

        if (count(Report::all()) > 0) {
            dump('Report no migrate');
        } else {

            $reportPpsd = ReportPssd::where('id','>','0')->get();
            $i=0;
            foreach ($reportPpsd as $item) {
                if ($item->zadach != 145
                    and $item->zadach != 0) {
                    $task = new Report();
                    $task->id = $item->id;
                    $task->task_id = (int)$item->zadach;
                    $task->user_id = $item->avtor;
                    $task->text = str_replace('?','Ы',$item->text);
//                    $task->file_hash = $item->file_md5;
//                    $task->file_name = $item->file_name;
                    $task->created_at = $item->time;
                    $task->updated_at = $item->time;
                    if ($item->del != null) {
                        $task->deleted_at = date("Y-m-d H:i:s");
                        $task->history=$item->del;
                    }
                    if ($item->avtor != 15) {
                        $task->save();
                        if ($item->file_md5) {
                            $file = New File();
                            $file->name = $item->file_name;
                            $file->hash = $item->file_md5;
                            $task->file()->save($file);
                        }

                    }
                    $i++;
                    //dump($task);
                }
            }
            dump('Report count:'. $i);
        }


    }
}

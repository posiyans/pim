<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\ViewReport;
use App\Modules\File\Models\File;
use App\Modules\Log\Models\Log;
use App\Modules\Protocol\Models\Partition;
use App\Modules\Protocol\Models\Protocol;
use App\Modules\Task\Models\Report;
use App\Modules\Task\Models\Task;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class Migration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'convert:bd';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Конвертировать базу';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->CreateConnection();
        $this->migrateUsers();
        $this->migrateProtocol();
        $this->migratePartitions();
        $this->migrateTask();
        $this->migrateReport();
        $this->migrateExecurers();
        return;
    }


    private function migrateExecurers()
    {
        foreach (Task::all() as $task) {
            $countReport[$task->id] = $task->report->count();
        }
//        dd($countReport);

        $i = 0;
        $report = DB::connection('old_pim')->table('contorl_zadach')->get();
        foreach ($report as $item) {
            if ($item->user != 14 and $item->user != 16) {
                $r = new ViewReport();
                $r->task_id = $item->zadach;
                $r->user_id = $item->user;
                $r->executor = 1;
                $r->done = $item->potvergdenie;
                $r->show = $item->show > $countReport[$task->id] ? $countReport[$task->id] : $item->show;
                $r->created_at = $item->time;
                try {
                    $r->save();
                } catch (\Exception $e) {
                }
                $i++;
            }
        }

        $report = DB::connection('old_pim')->table('contorl_moderat')->get();
        foreach ($report as $item) {
            if ($item->user != 14 and $item->user != 16) {
                $r = new ViewReport();
                $r->task_id = $item->zadach;
                $r->user_id = $item->user;
                $r->executor = 0;
                $r->show = $item->show > $countReport[$task->id] ? $countReport[$task->id] : $item->show;
                try {
                    $r->save();
                } catch (\Exception $e) {
                }
                $i++;
            }
        }
        dump('View count:' . $i);
    }

    private function migrateReport()
    {
        $reportPpsd = DB::connection('old_pim')->table('prim')->get();
        $i = 0;
        foreach ($reportPpsd as $item) {
            if ($item->zadach != 145 and $item->zadach != 0) {
                $task = new Report();
                $task->id = $item->id;
                $task->task_id = (int)$item->zadach;
                $task->user_id = $item->avtor;
                $task->text = $item->text;
//            $task->text = str_replace('?', 'Ы', $item->text);
                $task->created_at = $item->time;
                $task->updated_at = $item->time;
                if ($item->del != null) {
                    $task->deleted_at = date("Y-m-d H:i:s");
//                $task->history = $item->del; todo -->> to Log
                }
                if ($item->avtor != 15) {
                    $task->save();
                    if ($item->file_md5) {
                        $file = new File();
                        $file->name = $item->file_name;
                        $file->hash = $item->file_md5;
                        $file->user_id = $item->avtor;
                        $task->files()->save($file);
                    }
                }
                $i++;
                //dump($task);
            }
        }
        dump('Report count:' . $i);
    }


    private function migrateTask()
    {
        $pr = [];
        $ur = [];
        foreach (User::all() as $u) {
            $ur[trim($u->name)] = $u->id;
        }
        print_r($ur);
        echo PHP_EOL;
        foreach (Protocol::all() as $item) {
            $pr[$item['number']] = $item['id'];
        }
        $taskPpsd = DB::connection('old_pim')->table('zadach')->get();
        $i = 0;
        foreach ($taskPpsd as $item) {
            try {
                $task = new Task();
                $task->id = $item->id;
                $partition = Partition::where('protocol_id', $pr[$item->protokol])->where('number', (int)$item->nomer)->first();
                if ($partition) {
                    $task->partition_id = $partition->id;
                } else {
                    echo 'task error!!!', $task->id;
                    echo PHP_EOL;
                    $task->partition_id = 1;
                }
                $task->number = $item->nomer;
                if ($item->data_ispoln != '0000-00-00') {
                    if ($item->data_ispoln == '2019-09-00') {
                        $item->data_ispoln = '2019-09-01';
                    }
                    $task->data_ispoln = $item->data_ispoln;
                }
                $task->data_perenosa = $item->data_perenosa;
                $task->text = $item->text;
                $task->autor_id = $item->avtor;
                $task->executor = $item->ispolnitel;
                $task->protocol_id = $pr[$item->protokol];
//            $task->history = $item->hist; todo отправить в лог
//            $task->full_history = $item->hide_hist;todo отправить в лог
                $task->arxiv = $item->arxiv;
                $task->save();
                $i++;
                $hist = explode('<br>', $item->hist);
                foreach ($hist as $h) {
                    $tmp = explode('</i> ', $h);
                    if (count($tmp) > 1) {
                        $log = new Log();
                        $log->type = 'info';
                        $log->action = 'edit';
                        $t_create = str_replace('<i>', '', $tmp[0]);
                        $r = explode(' ', $tmp[1]);
                        $log->user_id = $ur[trim($r[0])] ?? null;
                        $log->value = ['text' => $tmp[1]];
                        $task->log()->save($log);
                        $log->created_at = $t_create;
                        $log->save();
                    }
                }
            } catch (\Exception $e) {
                echo $item->id . ' задача пропущена';
                echo PHP_EOL;
            }
            //dump($task);
        }
        dump('Task count:' . $i);
    }


    private function migratePartitions()
    {
        $pr = [];
        foreach (Protocol::all() as $item) {
//            if (isset($pr[$item['number']])) {
//                dump($item['number']);
//            }
            $pr[$item['number']] = $item['id'];
        }

        $i = 0;
        $partitionsPpsd = DB::connection('old_pim')->table('toping')->get();
        foreach ($partitionsPpsd as $item) {
            $part = new Partition();
            $part->protocol_id = $pr[$item->protokol];
            $part->number = $item->nomer;
            $part->text = $item->tema;
            $part->speaker = $item->kto;
            $part->save();
            if ($item->file_md5) {
                $file = new File();
                $file->name = $item->file_name;
                $file->hash = $item->file_md5;
                $file->user_id = 5;
                $part->files()->save($file);
            }
            $i++;
        }
        dump('Partition count: ' . $i);
    }


    private function CreateConnection()
    {
        $conf = config('database')['connections']['mysql'];
        $conf['database'] = 'ppsd_old';
        Config::set('database.connections.old_pim', $conf);
    }


    private function migrateProtocol()
    {
        $protoklsPpsd = DB::connection('old_pim')->table('protokol')->get();
        foreach ($protoklsPpsd as $item) {
            $protokol = new Protocol();
            $protokol->id = $item->id;
            $protokol->number = $item->nomer;
            $protokol->title = $item->prim;
            $descriptions['region'] = $item->mesto;
            $descriptions['president'] = $item->predsed;
            $descriptions['secretary'] = $item->secretar;
            $descriptions['date'] = $item->data;
            $descriptions['composition'] = $item->sostav;
            $protokol->descriptions = $descriptions;
            $protokol->save();
            if ($item->file_md5) {
                $file = new File();
                $file->name = $item->file_name;
                $file->hash = $item->file_md5;
                $file->hash = $item->file_md5;
                $file->user_id = 5;
                $protokol->files()->save($file);
            }
        }
        dump('Protocol Ok');
    }


    private function migrateUsers()
    {
        $userPpsd = DB::connection('old_pim')->table('users')->get();
        foreach ($userPpsd as $item) {
            $user = new User();
            $user->id = $item->user_id;
            $user->login = $item->user_login;
            $user->name = $item->name;
            $user->full_name = $item->full_name;
            $user->moderator = $item->moderator ?? false;
            $user->last_connect = $item->last_conect;
            $options = [
                'phone' => $item->phone,
                'color' => $item->color,
                'telegram' => $item->telegram
            ];
            $user->hide = $item->hide > 0 ? true : false;
            $user->options = $options;

            $t = [];
            if (isset($item->dostup)) {
                foreach (explode(",", $item->dostup) as $i) {
                    $t[] = (int)$i;
                }
            }
            $user->aliases = $t;
            $user->password = Hash::make($item->user_password);
            $user->save();
        }
        dump('User ok');
    }

}
<?php

namespace App\Modules\Protocol\Controllers;

use App\Http\Controllers\MyController;
use App\Models\ViewReport;
use App\Modules\File\Models\File;
use App\Modules\Log\Models\Log;
use App\Modules\Protocol\Models\Partition;
use App\Modules\Protocol\Models\Protocol;
use App\Modules\Task\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CreateProtocolController extends MyController
{


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user->moderator and $request->has('protokol') and $request->hasFile('file')) {
            DB::beginTransaction();
            try {
                $fileInput = $request->file('file');
                $md5 = $this->save_file($fileInput);
                $protokol_json = json_decode($request->get('protokol'));
                $protokol = new Protocol();
                $protokol->nomer = $protokol_json->nomer;
                $protokol->title = $protokol_json->title;
                $protokol->type = $protokol_json->type;
                $protokol->descriptions = $protokol_json->descriptions;
                $protokol->save();
                $file = new File();
                $file->name = $fileInput->getClientOriginalName();
                $file->hash = $md5;
                $protokol->files()->save($file);
                $log = new Log();
                $log->type = 'ok';
                $log->description = 'create protokol';
                $log->value = $protokol->toArray();
                $log->save();
                $data = date("d-m-Y H:i:s");
                $m_now = date("m");
                $y_now = date("Y");
                foreach ($protokol_json->partition as $partition_json) {
                    $partition = new Partition();
                    $partition->text = $partition_json->text;
                    $partition->number = $partition_json->number;
                    $partition->speaker = $partition_json->speaker;
                    $protokol->partition()->save($partition);
                    foreach ($partition_json->task as $task_json) {
                        $task = new Task();
                        $d_ispoln = trim($task_json->data_ispoln);
                        if ($d_ispoln) {
                            $temp = explode('.', $d_ispoln);
                            $task->data_ispoln = null;
                            if (count($temp) == 2) {
                                $d = $temp[0];
                                $m = $temp[1];
                                if ($m - $m_now > -6) {
                                    $y = $y_now;
                                } else {
                                    $y = $y_now + 1;
                                }
                                $task->data_ispoln = $y . '-' . $m . '-' . $d;
                            }
                            if (count($temp) == 3) {
                                $d = $temp[0];
                                $m = $temp[1];
                                $y = $temp[2];
                                $task->data_ispoln = $y . '-' . $m . '-' . $d;
                            }
                        } else {
                            $task->data_ispoln = null;
                        }
                        $task->number = $task_json->number;
                        $task->text = $task_json->text;
                        $task->autor_id = $user->id;
                        $task->executor = $task_json->executor;
                        $task->protocol_id = $protokol->id;
                        $task->history = '<i>' . $data . '</i> ' . $user->name . ' Создание задачи<br>';
                        $partition->task()->save($task);
                        if (count($task_json->users) == 0) {
                            throw new \Exception('Не выбран исполнитель');
                        } else {
                            foreach ($task_json->users as $executor_json) {
                                $executor = new ViewReport();
                                $executor->user_id = (int)$executor_json;
                                $executor->executor = 1;
                                $task->viewReport()->save($executor);
                            }
                        }
                    }
                }
                $log = new Log();
                $log->type = 'ok';
                $log->description = 'create protokol';
                $log->value = $protokol->toArray();
                $log->save();
                DB::commit();
                return response([$protokol]);
            } catch (\Exception $e) {
                DB::rollback();
                $text = 'При сохранении произошла ошибка, проверте данные протокола. ' . $e->getMessage();
                return response(['message' => $text], 400);
            }
        }
    }


}

<?php

namespace App\Modules\Protocol\Controllers;

use App\Http\Controllers\MyController;
use App\Models\ViewReport;
use App\Modules\File\Models\File;
use App\Modules\File\Repositories\FileRepository;
use App\Modules\Log\Classes\CreateInfoLog;
use App\Modules\Protocol\Models\Partition;
use App\Modules\Protocol\Models\Protocol;
use App\Modules\Task\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
        if ($user->moderator and $request->has('protocol') and $request->hasFile('file')) {
            DB::beginTransaction();
            try {
                $protokol_json = json_decode($request->get('protocol'));
                $protokol = new Protocol();
                $protokol->number = $protokol_json->number;
                $protokol->title = $protokol_json->title;
                $protokol->type = $protokol_json->type;
                $description = [
                    'date' => $protokol_json->date ?? '',
                    'region' => $protokol_json->region ?? '',
                    'president' => $protokol_json->president ?? '',
                    'secretary' => $protokol_json->secretary ?? '',
                    'composition' => $protokol_json->composition ?? '',
                ];
                $protokol->descriptions = $description;
                $protokol->save();
                (new CreateInfoLog($protokol))->text('Создал протокол')->run();
                $fileInput = $request->file('file');
                if ($fileInput) {
                    $md5 = md5_file($fileInput->getRealPath());
                    $file = new File();
                    $file->name = $fileInput->getClientOriginalName();
                    $file->hash = $md5;
                    $file->size = $fileInput->getSize();
                    $protokol->files()->save($file);
                    $path = FileRepository::getPathFromHash($md5, true);
                    Storage::putFileAs($path, $fileInput, $md5);
                    $text = 'Добавил файл ' . $file->name;
                    (new CreateInfoLog($protokol))->text($text)->run();
                }

                foreach ($protokol_json->partition as $partition_json) {
                    $partition = new Partition();
                    $partition->text = $partition_json->text;
                    $partition->number = $partition_json->number;
                    $partition->speaker = $partition_json->speaker;
                    $protokol->partition()->save($partition);
                    (new CreateInfoLog($partition))->text('Создал доклад')->run();
                    foreach ($partition_json->tasks as $task_json) {
                        $task = new Task();
                        $task->data_ispoln = $task_json->data_ispoln ?? null;
                        $task->number = $task_json->number;
                        $task->text = $task_json->text;
                        $task->autor_id = $user->id;
                        $task->executor = $task_json->executor;
                        $task->protocol_id = $protokol->id;
                        $partition->tasks()->save($task);
                        (new CreateInfoLog($task))->text('Создал задачу')->run();
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

<?php

namespace App\Http\Controllers\Ppsd;

use App\Http\Controllers\Controller;
use App\Models\ViewReport;
use App\Modules\File\Models\File;
use App\Modules\Log\Models\Log;
use App\Modules\Protocol\Models\Partition;
use App\Modules\Protocol\Models\Protocol;
use App\Modules\Task\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class ProtokolController extends Controller
{
    //


    public function getProtokolSource(Request $request)
    {
        $protokol = Protocol::find($request->id);
        $partitions = $protokol->partition;
        $data = [];
        //var_dump($partitions);
        foreach ($partitions as $partition) {
            $item = [];
            $item['id'] = $partition->id;
            $item['text'] = $partition->text;
            $item['number'] = $partition->number;
            $item['speaker'] = $partition->speaker;
            $item['timeLine'] = 60;
            $tasks = $partition->task;
            if ($tasks) {
                $item_task = [];
                foreach ($tasks as $task) {
                    //var_dump($task);
                    $t = [];
                    $t['text'] = $task->text;
                    $t['number'] = $task->number;
                    $t['speaker'] = $task->speaker;
                    $t['timeLine'] = 10;
                    $item_task[] = $t;
                }
                $item['children'] = $item_task;
            }
            $data[] = $item;
        }
        return response(['items' => $data, 'protokol' => $protokol]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //$total=Protocol::count();
        $limit = (int)$request->limit;
        $query = Protocol::query()->with('partition.task.viewReport');
        if (isset($request->archiv) && $request->archiv == 'true') {
            $query->whereNotNull('arxiv');
        } else {
            $query->whereNull('arxiv');
        }
        if ($request->type) {
            $type = $request->type;
            $query->where('type', $type);
        }
        if ($request->year) {
            $year = $request->year;
            $query->where('year', $year);
        }
        if ($request->sort) {
            if ($request->sort == '+id') {
                $query->orderBy('id', 'desc');
            } else {
                $query->orderBy('id', 'asc');
            }
        }
        $protokols = $query->paginate($limit);
        $total = $protokols->total();
        $data = [];
        foreach ($protokols as $protokol) {
            $item = [];
            $item['id'] = $protokol->id;
            $item['title'] = $protokol->title;
            $item['link'] = $request->limit;
            $item['type'] = $protokol->type;
            $item['descriptions'] = $protokol->descriptions;
            $item['PercentComplete'] = $protokol->getPercentComplete();
            $data[] = $item;
        }
        return response(['total' => $total, 'items' => ['data' => $data]]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        echo 'create';
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store_old(Request $request)
    {
        $protokol = Protocol::find($request->id);
        $partitions = $protokol->partition;
        $data = [];
        //var_dump($partitions);
        foreach ($partitions as $partition) {
            $item = [];
            $item['id'] = $partition->id;
            $item['text'] = $partition->text;
            $item['number'] = $partition->number;
            $item['speaker'] = $partition->speaker;
            $item['timeLine'] = 60;
            $tasks = $partition->task;
            if ($tasks) {
                $item_task = [];
                foreach ($tasks as $task) {
                    //var_dump($task);
                    $t = [];
                    $t['text'] = $task->text;
                    $t['number'] = $task->number;
                    $t['speaker'] = $task->speaker;
                    $t['timeLine'] = 10;
                    $item_task[] = $t;
                }
                $item['children'] = $item_task;
            }
            $data[] = $item;
        }
        return response(['items' => $data, 'protokol' => $protokol]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->hasRole('admin') and Input::has('protokol') and Input::hasFile('file')) {
            DB::beginTransaction();
            try {
                $fileInput = Input::file('file');
                $md5 = $this->save_file($fileInput);
                $protokol_json = json_decode(Input::get('protokol'));
                $protokol = new Protocol();
                $protokol->nomer = $protokol_json->nomer;
                $protokol->title = $protokol_json->title;

//                $protokol->file_hash = $md5;
//                $protokol->file_name = $file->getClientOriginalName();
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
                        $task->protokol_id = $protokol->id;
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


    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        //
        $protokol = Protocol::with('partition.task.viewReport')->find($id);
        return response(['protokol' => $protokol]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {
        $user = Auth::user();
        if ($user->hasRole('admin')) {
            $action = $request->type;
            if ($action == 'protokolToArchiv') {
                $protokol = Protocol::find($id);

                if ($protokol) {
                    $today = date('Y-m-d H:m:s');
                    $text = '<i>' . $today . '</i> ' . $user->name . ' перенес данный протокол и задачи в архив';
                    $protokol->arxiv = '<i>' . $today . '</i> ' . $user->name . ' перенес протокол в архив<br>';
                    $protokol->save();
                    $tasks = $protokol->task();
                    foreach ($tasks as $task) {
                        $task->history .= $text;
                        $task->arxiv = $text;
                        $task->save();
                    }
                    return response('ok');
                }
                return response(['protokolToArchiv']);
            }
            if ($action == 'protokolUpdate') {
                $protokol = Protocol::with('partition.task.viewreport')->find($id);
                $protokol_old = clone $protokol;
                //$protokol_old = $protokol_old->toArray();
                if ($protokol and $protokol->id == $request->protokol['id']) {
                    $protokol_json = $request->protokol;
                    $protokol->title = $protokol_json['title'];
                    $protokol->type = $protokol_json['type'];
                    $descriptions['date'] = $protokol_json['descriptions']['date'];
                    $descriptions['region'] = $protokol_json['descriptions']['region'];
                    $descriptions['president'] = $protokol_json['descriptions']['president'];
                    $descriptions['secretary'] = $protokol_json['descriptions']['secretary'];
                    $descriptions['composition'] = $protokol_json['descriptions']['composition'];
                    $protokol->descriptions = $descriptions;
                    $protokol->save();
                    foreach ($protokol_json['partition'] as $partition_json) {
                        $partition = Partition::find($partition_json['id']);
                        $partition->text = $partition_json['text'];
                        $partition->number = $partition_json['number'];
                        $partition->speaker = $partition_json['speaker'];
                        $partition->save();
                    }
                    $protokol = Protocol::with('partition.task.viewreport')->find($id);
                    Log::saveDiff($protokol, $protokol_old);
                    return response([$request->protokol]);
                }
                return response(['error-id']);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    }

    public function exportProtokol($id, Request $request)
    {
        $user = Auth::user();
        if ($user->hasRole('admin')) {
            if (strlen($id) == 32) {
                $item = Partition::where('file_hash', $id)->first();
            } else {
                $item = Protocol::find($id);
            }
            if ($item) {
                $file = $item->file;
                $md5 = $file[0]->hash;
                $name = $file[0]->name;
                $path = '../storage/app/file/ppsd/pr/' . substr($md5, 0, 2) . '/' . $md5;
                return response()->download($path, $name, $headers = ['filename' => $name]);
            }
            return response(['export', $id]);
        }
    }

}

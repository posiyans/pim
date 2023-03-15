<?php

namespace App\Modules\Protocol\Controllers;

use App\Http\Controllers\MyController;
use App\Modules\Log\Classes\CreateInfoLog;
use App\Modules\Protocol\Models\Protocol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

class MoveProtocolToArchiveController extends MyController
{
    public function __construct()
    {
        $this->middleware('only_moderator');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $id = $request->id;
        $user = Auth::user();
        $protokol = Protocol::find($id);
        if ($protokol) {
            $today = date('Y-m-d H:m:s');
            $text = '<i>' . $today . '</i> ' . $user->name . ' перенес данный протокол и задачи в архив';
            $protokol->arxiv = '<i>' . $today . '</i> ' . $user->name . ' перенес протокол в архив<br>';
            $protokol->save();
            $tasks = $protokol->tasks();
            (new CreateInfoLog($protokol))->text('Перенос протокола в архив')->run();
            foreach ($tasks as $task) {
                $task->arxiv = $text;
                $task->save();
                (new CreateInfoLog($task))->text('Перенос протокола задачи в архив')->run();
            }
        }
        return response('ok');
    }


}

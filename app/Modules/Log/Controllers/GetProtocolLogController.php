<?php

namespace App\Modules\Log\Controllers;


use App\Http\Controllers\MyController;
use App\Modules\Protocol\Models\Protocol;
use Illuminate\Http\Request;

use function App\Modules\Docx\Controllers\mb_strtolower;

class GetProtocolLogController extends MyController
{
    public function __construct()
    {
        $this->middleware('only_moderator');
    }


    public function index(Request $request)
    {
        $task_id = $request->id;
        $model = Protocol::find($task_id);
        $log = $model->log;
        return response($log);
    }

}

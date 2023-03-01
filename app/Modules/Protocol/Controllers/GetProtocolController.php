<?php

namespace App\Modules\Protocol\Controllers;

use App\Http\Controllers\MyController;
use App\Modules\Protocol\Models\Protokol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class GetProtocolController extends MyController
{


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $id = $request->id;
        $protokol = Protokol::with('partition.task.viewReport')->find($id);
        return response($protokol);
    }


}
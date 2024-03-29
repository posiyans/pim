<?php

namespace App\Modules\Protocol\Controllers;

use App\Http\Controllers\MyController;
use App\Modules\Protocol\Models\Protocol;
use Illuminate\Http\Request;

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
        $protokol = Protocol::with('partition.task.viewReport')->with('files')->find($id);
        return response($protokol);
    }


}

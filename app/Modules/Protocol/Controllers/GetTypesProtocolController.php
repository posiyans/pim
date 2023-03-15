<?php

namespace App\Modules\Protocol\Controllers;

use App\Http\Controllers\MyController;
use App\Modules\Protocol\Models\TypeProtocolModel;
use Illuminate\Http\Request;

class GetTypesProtocolController extends MyController
{


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $protokol = TypeProtocolModel::all();
        return response($protokol);
    }


}

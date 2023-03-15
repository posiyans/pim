<?php

namespace App\Modules\Protocol\Controllers;

use App\Http\Controllers\MyController;
use App\Modules\Protocol\Models\TypeProtocolModel;
use Illuminate\Http\Request;

class CreateTypeProtocolController extends MyController
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
        if ($request->has('name')) {
            $type = new TypeProtocolModel();
            $type->name = $request->name;
            $type->save();
            return response($type);
        }
        return response(false);
    }


}

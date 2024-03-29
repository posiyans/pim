<?php

namespace App\Modules\Protocol\Controllers;

use App\Http\Controllers\MyController;
use App\Modules\Protocol\Models\Protocol;
use App\Modules\Protocol\Resources\ProtocolResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class GetProtocolListController extends MyController
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
        //$total=Protocol::count();
        $limit = (int)$request->limit;
        $query = Protocol::query()->with('partition.task.viewReport');
        if ($request->archiv) {
            $query->whereNotNull('arxiv');
        } else {
            $query->whereNull('arxiv');
        }
        if ($request->type) {
            $type = $request->type;
            $query->where('type_id', $type);
        }
        if ($request->find) {
            $query->where('title', 'like', '%' . $request->find . '%');
        }
        $query->orderBy('id', 'desc');
//        if ($request->sort) {
//            if ($request->sort == '+id') {
//                $query->orderBy('id', 'asc');
//            } else {
//                $query->orderBy('id', 'desc');
//            }
//        }
        $protokols = $query->paginate($limit);
        return ProtocolResource::collection($protokols)->response()->getData(true);
    }

}

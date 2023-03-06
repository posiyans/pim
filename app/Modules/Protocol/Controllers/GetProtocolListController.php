<?php

namespace App\Modules\Protocol\Controllers;

use App\Http\Controllers\MyController;
use App\Modules\Protocol\Models\Protocol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class GetProtocolListController extends MyController
{


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
        return response(['total' => $total, 'data' => $data]);
    }


}

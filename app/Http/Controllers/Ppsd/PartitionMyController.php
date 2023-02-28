<?php

namespace App\Http\Controllers\Ppsd;

use App\Http\Controllers\Controller;
use App\Models\Partition;
use App\Modules\Log\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

class PartitionMyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
        if ($user->hasRole('admin')
            and $request->type == 'uploadPartitionFile'
            and Input::hasFile('file')) {
            $file = Input::file('file');
            $md5 = $this->save_file($file);
            $partition = Partition::find($request->partition);
            $partition_old = clone $partition;
            $partition->file_hash = $md5;
            $partition->file_name = $file->getClientOriginalName();
            $partition->save();
            Log::saveDiff($partition, $partition_old);
            return $this->response(['ok']);
        }
        return $this->response(['error'], 401);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
    public function update(Request $request, $id)
    {
        //
        $file = Input::file('file');
        //$name= $file->getClientOriginalName();
        $name = '';
        return $this->response(['store', $file, $name, $request->file('file')]);
//        $file = Input::file('file');
//        //$name= $file->getClientOriginalName();
//        $name= '';
//        return $this->response(['update'=>'', 'file'=>$name, 'reg'=>$request]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

<?php

namespace App\Modules\Protocol\Controllers;

use App\Http\Controllers\MyController;
use App\Modules\Log\Models\Log;
use App\Modules\Protocol\Models\Partition;
use App\Modules\Protocol\Models\Protokol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

class UpdateProtocolController extends MyController
{


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $id = $request->id;
        $user = Auth::user();
        if ($user->moderator) {
            $protokol = Protokol::with('partition.task.viewreport')->find($id);
            $protokol_old = clone $protokol;
            if ($protokol) {
                $protokol_json = $request;
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
                $protokol = Protokol::with('partition.task.viewreport')->find($id);
                Log::saveDiff($protokol, $protokol_old);
                return response($protokol);
            }
        }
    }


}

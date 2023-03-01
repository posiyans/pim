<?php

namespace App\Models\Ppsd;

use App\Modules\File\Models\File;
use App\Modules\Protocol\Models\Protokol;
use Illuminate\Database\Eloquent\Model;

class ProtokolsPpsd extends Model
{
    protected $connection = 'mysql_ppsd';
    protected $table = 'protokol';
    public $timestamps = false;

    public static function ProtokolsMigrate()
    {
        if (count(Protokol::all()) > 0) {
            dump('Protokol no migrate');
        } else {
            $protoklsPpsd = ProtokolsPpsd::all();
            foreach ($protoklsPpsd as $item) {
                $protokol = new Protokol();
                $protokol->id = $item->id;
                $protokol->nomer = $item->nomer;
                $protokol->title = $item->prim;
//                $protokol->file_hash = $item->file_md5;
//                $protokol->file_name = $item->file_name;
                $descriptions['region'] = $item->mesto;
                $descriptions['president'] = $item->predsed;
                $descriptions['secretary'] = $item->secretar;
                $descriptions['date'] = $item->data;
                $descriptions['composition'] = $item->sostav;
                $protokol->descriptions = $descriptions;
                $protokol->save();
                if ($item->file_md5) {
                    $file = new File();
                    $file->name = $item->file_name;
                    $file->hash = $item->file_md5;
                    $protokol->file()->save($file);
                }
                //dump($protokol);
            }
            dump('Protokol Ok');
        }
    }


}

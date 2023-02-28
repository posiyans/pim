<?php

namespace App\Models\Ppsd;

use App\Models\File;
use App\Modules\Protocol\Models\Partition;
use App\Modules\Protocol\Models\Protokol;
use Illuminate\Database\Eloquent\Model;

class PartitionPpsd extends Model
{
    //
    protected $connection = 'mysql_ppsd';
    protected $table = 'toping';
    public $timestamps = false;


    public static function PartitionMigrate()
    {
        foreach (Protokol::all() as $item) {
            if (isset($pr[$item['nomer']])) {
                dump($item['nomer']);
                //dump($pr[$item['nomer']] );
            }
            $pr[$item['nomer']] = $item['id'];
        }
        //dump($pr);

        $i = Partition::all();
        if (count($i) > 0) {
            dump('Partition no migrate');
        } else {
            //$pr=[];
            $i = 0;
            foreach (PartitionPpsd::all() as $item) {
                //dump($item->tema);
                $part = new Partition();
                $part->protokol_id = $pr[$item->protokol];
                $part->number = $item->nomer;
                $part->text = $item->tema;
                $part->speaker = $item->kto;
                $part->save();
                if ($item->file_md5) {
                    $file = new File();
                    $file->name = $item->file_name;
                    $file->hash = $item->file_md5;
                    $part->file()->save($file);
                }
                $i++;
            }
            dump('Partition count: ' . $i);
        }
    }

}

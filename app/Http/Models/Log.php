<?php

namespace App\Http\Models;

use App\MyModel;
use Illuminate\Support\Facades\Auth;

class Log extends MyModel
{
    //
    protected $casts = [
        'value' => 'array',
    ];

    /**
     * Log constructor.
     */
    public function __construct()
    {
        if (Auth::check()) {
            $this->user_id = Auth::user()->id;
        }
        $this ->action = debug_backtrace()[1]['function'];
        $this ->user_agent = $_SERVER['REMOTE_ADDR'].'/'.$_SERVER['HTTP_USER_AGENT'];
    }

    /**
     * Получить все модели, обладающие commentable.
     */
    public function commentable()
    {
        return $this->morphTo();
    }

    public static function saveDiff($objNew, $objOld, $description=false){
            $log = new Log();
            if ($diff = $log->diff($objNew, $objOld)) {
                $log->type = 'ok';
                if ($description) {
                    $log->description = $description;
                } else {
                    $log->description = 'change to ' . get_class($objOld);
                }
                $log->value = $diff;
                $objOld->log()->save($log);
            }
    }


    public function diff($objNew, $objOld)
    {
        $diff = [];
        if (is_object($objNew)) {
            $objNew = $objNew->toArray();
        }
        if (is_object($objOld)) {
            $objOld = $objOld->toArray();
        }
        foreach ($objNew as $key => $value){
            if (!is_array($value)){
                if ($objOld[$key] != $objNew[$key]) {
                    $diff[$key] = ['old'=>$objOld[$key], 'new'=>$objNew[$key]];
                }
            } else {
                if (isset($objOld[$key])) {
                    $val = $this->diff($objNew[$key], $objOld[$key]);
                    if ($val and count($val) > 0) {
                        $diff[$key] = $val;
                    }
                }else {
                    $diff[$key] = ['old' => '', 'new' => $objNew[$key]];
                }
            }
        }
        if (count($diff) > 0){
            return $diff;
        }
        return false;
    }
}


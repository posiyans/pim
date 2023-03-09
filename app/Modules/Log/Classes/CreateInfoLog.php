<?php

namespace App\Modules\Log\Classes;


use App\Modules\Log\Models\Log;
use Illuminate\Support\Facades\Auth;

class CreateInfoLog
{
    private $log;

    public function __construct($model)
    {
        $this->log = new Log();
        $this->log->type = 'info';
        $this->log->value = ['text' => ''];
        $this->user_id = Auth::user() ?? null;
        $this->log->commentable_type = get_class($model);
        $this->log->commentable_id = $model->id;
    }

    public function text($text)
    {
        $this->log->value = [
            'text' => $text
        ];
        return $this;
    }

    public function run()
    {
        if ($this->log->save()) {
            return $this->log;
        }
        return throw new \Exception('Ошибка сохранения логов');
    }
}
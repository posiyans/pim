<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MyModel extends Model
{
    /**
     * Получить log модели
     */
    public function log()
    {
        return $this->morphMany('App\Modules\Log\Models\Log', 'commentable');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     * @deprecated
     */
    public function file()
    {
        return $this->morphMany('App\Modules\File\Models\File', 'commentable');
    }

    public function files()
    {
        return $this->morphMany('App\Modules\File\Models\File', 'commentable');
    }
}

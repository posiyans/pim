<?php

namespace App\Modules\File\Models;

use App\MyModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;

class File extends MyModel
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    //
    public function commentable()
    {
        return $this->morphTo();
    }


    public function __construct(array $attributes = [])
    {
        if (Auth::check()) {
            $this->user_id = Auth::user()->id;
        }
        $this->uid = Uuid::uuid4()->toString();
        return parent::__construct($attributes);
    }

}

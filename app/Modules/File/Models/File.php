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

    /**
     * File constructor.
     */
    public function __construct()
    {
        if (Auth::check()) {
            $this->user_id = Auth::user()->id;
            $this->uid = Uuid::uuid4()->toString();
        }
    }
}

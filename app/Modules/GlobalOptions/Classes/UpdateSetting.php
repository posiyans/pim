<?php

namespace App\Modules\GlobalOptions\Classes;

use App\Modules\GlobalOptions\Models\Setting;

class UpdateSetting
{

    private $opt;

    public function __construct(Setting $opt)
    {
        $this->opt = $opt;
    }

    public function type($type)
    {
        $this->opt->type = $type;
        return $this;
    }

    public function value($value)
    {
        if ($this->opt->type == 'array') {
            $this->opt->value = $value;
        } else {
            $this->opt->value = $value;
        }
        return $this;
    }


    public function update()
    {
        //todp Log
        $this->opt->save();
        return $this->opt;
    }
}

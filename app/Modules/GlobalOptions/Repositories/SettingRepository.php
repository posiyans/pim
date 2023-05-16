<?php

namespace App\Modules\GlobalOptions\Repositories;

use App\Modules\GlobalOptions\Models\Setting;

class SettingRepository
{

    public function getOption($name)
    {
        $opt = Setting::where('field', $name)->first();
        return $opt;
    }

    public function getOptionValue(string $name, $default = null, $noConvert = false)
    {
        $opt = $this->getOption($name);
        if ($opt) {
            if ($noConvert) {
                return $opt->value;
            } else {
                return $this->convertFormat($opt);
            }
        }
        return $default;
    }


    private function convertFormat(Setting $opt)
    {
        if ($opt->type == 'string') {
            return $opt->value;
        }
        if ($opt->type == 'boolean') {
            return (boolean)$opt->value;
        }
        if ($opt->type == 'array') {
            return $opt->value;
        }
        if ($opt->type == 'password') {
            if ($opt->value) {
                return $this->getP(strlen($opt->value));
            }
            return '';
        }
        return $opt->value;
    }

    private function getP($len = 16)
    {
        $str = '';
        for ($i = 0; $i < $len; $i++) {
            $str .= '*';
        }
        return $str;
    }
}

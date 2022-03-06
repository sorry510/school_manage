<?php

namespace App\Admin\Models;

use Encore\Admin\Auth\Database\Administrator;

class Admin extends Administrator
{
    public function setImgsAttribute($imgs)
    {
        if (is_array($imgs)) {
            $this->attributes['imgs'] = json_encode($imgs);
        }
    }

    public function getImgsAttribute($imgs)
    {
        $imgs = json_decode($imgs, true);
        return $imgs ?? [];
    }
}

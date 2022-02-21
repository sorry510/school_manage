<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class School extends Base
{
    use SoftDeletes;

    protected $table = 'school';
}

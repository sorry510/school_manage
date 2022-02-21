<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Auth
{
    use SoftDeletes;

    protected $table = 'student';
}

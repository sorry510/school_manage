<?php

namespace App\Models;

class SchoolApply extends Base
{
    const STATUS_SUCCESS = 1;
    const STATUS_FAILED = 2;
    const STATUS_PENDING = 3;

    protected $table = 'school_apply';
}

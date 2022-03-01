<?php

namespace App\Models;

class LineUserRelation extends Base
{
    protected $table = 'line_user_relation';

    const TYPE_TEACHER = 1;
    const TYPE_STUDENT = 2;
}

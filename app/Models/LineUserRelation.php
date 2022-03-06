<?php

namespace App\Models;

class LineUserRelation extends Base
{
    protected $table = 'line_user_relation';

    public const TYPE_TEACHER = 1;
    public const TYPE_STUDENT = 2;
}

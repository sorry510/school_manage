<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Auth
{
    use SoftDeletes;

    public const ONLINE = 1;
    public const OFFLINE = 2;

    public const LINE_TYPE_TEXTS = [
        self::ONLINE => '在线',
        self::OFFLINE => '离线',
    ];

    protected $table = 'student';

    public function school()
    {
        return $this->belongsTo(School::class, 'school_id');
    }
}

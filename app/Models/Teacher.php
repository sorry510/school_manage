<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Teacher extends Auth
{
    use SoftDeletes;

    const STATUS_ACTIVE = 1;
    const STATUS_NO_ACTIVE = 2;

    const STATUS_TEXTS = [
        self::STATUS_ACTIVE => '已激活',
        self::STATUS_NO_ACTIVE => '未激活',
    ];

    const ONLINE = 1;
    const OFFLINE = 2;

    const LINE_TYPE_TEXTS = [
        self::ONLINE => '在线',
        self::OFFLINE => '离线',
    ];

    protected $table = 'teacher';

    public function schoolApply()
    {
        return $this->hasMany(SchoolApply::class);
    }
}

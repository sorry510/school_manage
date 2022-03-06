<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Teacher extends Auth
{
    use SoftDeletes;

    public const STATUS_ACTIVE = 1;
    public const STATUS_NO_ACTIVE = 2;

    public const STATUS_TEXTS = [
        self::STATUS_ACTIVE => '已激活',
        self::STATUS_NO_ACTIVE => '未激活',
    ];

    public const ONLINE = 1;
    public const OFFLINE = 2;

    public const LINE_TYPE_TEXTS = [
        self::ONLINE => '在线',
        self::OFFLINE => '离线',
    ];

    protected $table = 'teacher';

    public function schoolApply()
    {
        return $this->hasMany(SchoolApply::class);
    }
}

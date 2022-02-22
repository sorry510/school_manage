<?php

namespace App\Models;

class SchoolApply extends Base
{
    const STATUS_SUCCESS = 1;
    const STATUS_FAILED = 2;
    const STATUS_PENDING = 3;

    const STATUS_TEXTS = [
        self::STATUS_SUCCESS => '成功',
        self::STATUS_FAILED => '拒绝',
        self::STATUS_PENDING => '申请中',
    ];

    const STATUS_COLORS = [
        self::STATUS_SUCCESS => 'success',
        self::STATUS_FAILED => 'danger',
        self::STATUS_PENDING => 'info',
    ];

    protected $table = 'school_apply';

    protected $dispatchesEvents = [
        'saved' => \App\Events\SchoolApplyEvent::class, // 监听更新事件
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}

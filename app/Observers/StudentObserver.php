<?php

namespace App\Observers;

use App\Models\Student;
use Illuminate\Support\Facades\Hash;

class StudentObserver
{
    /**
     * @return void
     */
    public function saving(Student $model)
    {
        if (empty($model->password)) {
            unset($model->password);
        } else {
            // TODO 查询时不要查询密码字段，否则更新时会改变密码
            $model->password = Hash::make($model->password); // 加密密码
        }
    }
}

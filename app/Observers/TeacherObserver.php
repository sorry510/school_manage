<?php

namespace App\Observers;

use App\Models\Teacher;
use Illuminate\Support\Facades\Hash;

class TeacherObserver
{
    /**
     * @return void
     */
    public function saving(Teacher $model)
    {
        if (empty($model->password)) {
            unset($model->password);
        } else {
            $model->password = Hash::make($model->password); // 加密密码
        }
    }
}

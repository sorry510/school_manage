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
            $model->password = Hash::make($model->password); // 加密密码
        }
    }
}

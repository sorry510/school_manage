<?php

namespace App\Observers;

use App\Mail\TeacherRegisterMail;
use App\Models\Teacher;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class TeacherObserver
{

    /**
     * @return void
     */
    public function created(Teacher $model)
    {
        if ($model->status === Teacher::STATUS_NO_ACTIVE) {
            // 注册成功，发送邮箱
            Mail::to($model->email)
                ->send(new TeacherRegisterMail([
                    'teacher_id' => $model->id,
                ]));
            // TODO 改为队列方式
            // Mail::to($model->email)
            //     ->queue((new TeacherRegisterMail([
            //         'teacher_id' => $model->id,
            //     ]))->onQueue('emails'));
        }
    }
    /**
     * @return void
     */
    public function saving(Teacher $model)
    {
        if (empty($model->password)) {
            // 没有填写密码
            unset($model->password);
        } else {
            // TODO 查询时不要查询密码字段，否则更新时会改变密码
            $model->password = Hash::make($model->password); // 加密密码
        }
    }
}

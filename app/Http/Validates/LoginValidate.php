<?php

namespace App\Http\Validates;

use Illuminate\Validation\Rule;
use Sorry510\Annotations\Validate\Validate;

class LoginValidate extends Validate
{
    //验证规则
    protected function rule(): array
    {
        return [
            'username' => 'required',
            'password' => 'required|min:4',
            'type' => ['required', Rule::in(['teacher', 'student'])],
        ];
    }

    //提示信息 attribute是占位符，这里是custom方法的value
    protected $message = [
        'required' => ':attribute不能为空',
        'min' => ':attribute长度不符合规范',
        'in' => ':attribute不符合规范',
    ];

    // 自定义字段名称，提示的时候用到
    protected $custom = [
        'username' => '用户名',
        'password' => '密码',
        'type' => '类型',
    ];

    /**
     * @var Array
     */
    protected $scene = [
        'user' => ["username", "password", 'type'],
    ];
}

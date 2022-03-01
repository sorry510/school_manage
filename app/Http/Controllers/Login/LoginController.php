<?php

namespace App\Http\Controllers\Login;

use App\Constants\ErrorCode;
use App\Http\Controllers\Controller;
use App\Http\Validates\LoginValidate;
use App\Models\LineUser;
use App\Models\LineUserRelation;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Sorry510\Annotations\Validator;

class LoginController extends Controller
{
    /**
     * @OA\post(
     *     tags={"用户认证"},
     *     path="/api/login-in",
     *     summary="用户登录",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="username", type="string", description="用户名"),
     *                 @OA\Property(property="password", type="string", description="密码"),
     *                 @OA\Property(property="type", type="string", description="类型[teacher:教师,student:学生]"),
     *                 required={"username", "password", "type"}
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Success", @OA\JsonContent(
     *         @OA\Property(property="code", type="integer", description="返回码"),
     *         @OA\Property(property="message", type="string", description="错误信息"),
     *         @OA\Property(property="data", type="object", description="返回数据",
     *             @OA\Property(property="token", type="string", description="用户token"),
     *             @OA\Property(property="type", type="string", description="类型")
     *         ),
     *         @OA\Property(property="timestamp", type="integer", description="服务器响应的时间戳")
     *     ))
     * )
     *
     * @Validator(class=LoginValidate::class, scene="user")
     */
    public function loginIn(Request $request)
    {
        // 接受参数
        $params = $request->only("username", "password", "type");
        try {
            $errorCode = ErrorCode::SUCCESS;
            $data = [];
            if ($params['type'] === 'teacher') {
                [$errorCode, $data] = $this->loginTeacher($params);
            } else if ($params['type'] === 'student') {
                [$errorCode, $data] = $this->loginStudent($params);
            } else {
                $errorCode = ErrorCode::ERROR;
            }
            return $this->resJson($errorCode, $data);
        } catch (\Throwable $e) {
            return $this->resJson(ErrorCode::ERROR, $e->getMessage());
        }
    }

    protected function loginTeacher($data)
    {
        // 获取用户信息
        $user = Teacher::where('email', $data['username'])->first();
        if (!$user) {
            return [ErrorCode::LOGIN_ERROR, ""];
        }
        $password = $user->password;
        unset($user->password);
        // 验证用户是否被启用
        if ($user->status != "1") {
            return [ErrorCode::USER_FREEZE, ""];
        }
        // 记录登录信息
        $user->last_login_time = date("Y-m-d H:i:s");
        $user->last_login_ip = request()->ip();

        // 验证密码
        if (!Hash::check($data['password'], $password)) {
            // 失败次数加一
            $user->login_failure += 1;
            $user->save();
            return [ErrorCode::LOGIN_ERROR, ""];
        }
        // 失败次数清零
        $user->login_failure = 0;
        $user->online = 1;
        $user->save();
        $token = $user->createToken($data["type"], ['teacher']);
        return [ErrorCode::SUCCESS, ['token' => $token->accessToken, 'type' => 'teacher']];
    }

    protected function loginStudent($data)
    {
        // 获取用户信息
        $user = Student::where('account', $data['username'])->first();
        if (!$user) {
            return [ErrorCode::LOGIN_ERROR, ""];
        }
        $password = $user->password;
        unset($user->password);

        // 记录登录信息
        $user->last_login_time = date("Y-m-d H:i:s");
        $user->last_login_ip = request()->ip();

        // 验证密码
        if (!Hash::check($data['password'], $password)) {
            // 失败次数加一
            $user->login_failure += 1;
            $user->save();
            return [ErrorCode::LOGIN_ERROR, ""];
        }

        // 失败次数清零
        $user->login_failure = 0;
        $user->online = 1;
        $user->save();
        $token = $user->createToken($data["type"], ['student']);
        return [ErrorCode::SUCCESS, ['token' => $token->accessToken, 'type' => 'student']];
    }

    /**
     * @OA\delete(
     *     tags={"用户认证"},
     *     path="/api/{type}/login-out",
     *     summary="退出登录",
     *     @OA\Response(response=200, description="Success", @OA\JsonContent(
     *         @OA\Property(property="code", type="integer", description="返回码"),
     *         @OA\Property(property="message", type="string", description="错误信息"),
     *         @OA\Property(property="data", type="object", description="返回数据"),
     *         @OA\Property(property="timestamp", type="integer", description="服务器响应的时间戳")
     *     ))
     * )
     */
    public function loginOut(Request $request)
    {
        return $this->resJson(ErrorCode::SUCCESS);
    }

    /**
     * @OA\post(
     *     tags={"用户认证"},
     *     path="/api/register",
     *     summary="教师注册",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="name", type="string", description="用户名"),
     *                 @OA\Property(property="email", type="string", description="邮箱"),
     *                 @OA\Property(property="password", type="string", description="密码"),
     *                 required={"name", "email", "password"}
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Success", @OA\JsonContent(
     *         @OA\Property(property="code", type="integer", description="返回码"),
     *         @OA\Property(property="message", type="string", description="错误信息"),
     *         @OA\Property(property="data", type="object", description="返回数据"),
     *         @OA\Property(property="extra", type="object", description="返回额外数据"),
     *         @OA\Property(property="timestamp", type="integer", description="服务器响应的时间戳")
     *     ))
     * )
     */
    public function registerTeacher(Request $request)
    {
        $params = $request->only("name", "email", "password");
        try {
            // 注册验证
            $signRes = $this->checkRegister($params);
            if ($signRes != ErrorCode::SUCCESS) {
                return $this->resJson($signRes);
            }

            $data = [
                "name" => $params['name'],
                "email" => $params['email'],
                // "password" => Hash::make($params['password']), // 改为观察者实现
                "password" => $params['password'],
                "status" => 1, // 直接激活
            ];
            $user = Teacher::create($data);
            // TODO 注册成功，发送邮箱
            return $this->resJson(ErrorCode::SUCCESS, $user);
        } catch (\Throwable $e) {
            return $this->resJson(ErrorCode::ERROR, $e->getMessage());
        }
    }

    protected function checkRegister($data): int
    {
        // 验证邮箱是否已注册
        if (Teacher::where('email', $data['email'])->first()) {
            return ErrorCode::EMAIL_EXIST;
        }
        return ErrorCode::SUCCESS;
    }

    /**
     * line Socialite login
     */
    public function lineLogin()
    {
        return Socialite::driver('line')->redirect();
    }

    /**
     * line Socialite login callback
     */
    public function lineCallback()
    {
        try {
            $user = Socialite::driver('line')->user();

            // $user = new \StdClass;
            // $user->id = 'U34a89770e846f5205ce6e786b6bf3895';
            // $user->name = 'sorry510';
            // $user->avatar = 'https://profile.line-scdn.net/0hxlRcz8k_J0NvGjM8M-JYFFNfKS4YNCELF3lgLUtKcSMWejQcAXo_cEMbeSFCeWdCAX84dRgdLXQV';
            // $user->email = null;

            $lineUser = LineUser::where('id', $user->id)->first();
            if (!$lineUser) {
                $lineUser = new LineUser();
                $lineUser->id = $user->id;
            }
            $lineUser->name = $user->name ?? '';
            $lineUser->avatar = $user->avatar ?? '';
            $lineUser->email = $user->email ?? '';
            $lineUser->save();

            $teachers = []; // 已绑定的教师
            $students = []; // 已绑定的学生
            $relations = LineUserRelation::select('line_user_id', 'relation_id', 'type')->where('line_user_id', $user->id)->get();
            foreach ($relations as $relation) {
                if ($relation->type === LineUserRelation::TYPE_TEACHER) {
                    $teacher = Teacher::where('id', $relation->relation_id)->first();
                    $teachers[] = [
                        'line_user_id' => $relation->line_user_id,
                        'relation_id' => $relation->relation_id,
                        'type' => $relation->type,
                        'name' => $teacher->name,
                    ];
                } else if ($relation->type === LineUserRelation::TYPE_STUDENT) {
                    $student = Student::where('id', $relation->relation_id)->first();
                    $students[] = [
                        'line_user_id' => $relation->line_user_id,
                        'relation_id' => $relation->relation_id,
                        'type' => $relation->type,
                        'name' => $student->name,
                    ];
                }
            }

            return view('line.login', [
                'result' => [
                    'line' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'avatar' => $user->avatar,
                        'email' => $user->email,
                    ],
                    'relations' => [
                        'teachers' => $teachers,
                        'students' => $students,
                    ],
                ],
                'domain' => config('app.url'),
            ]);
        } catch (\Throwable $e) {
            return view('line.loginfailed');
        }
    }

    /**
     * @OA\post(
     *     tags={"用户认证"},
     *     path="/api/line/login-in",
     *     summary="LINE用户登录",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="line_id", type="string", description="line_id"),
     *                 @OA\Property(property="relation_id", type="string", description="relation_id"),
     *                 @OA\Property(property="type", type="string", description="类型"),
     *                 required={"line_id", "relation_id", "type"}
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Success", @OA\JsonContent(
     *         @OA\Property(property="code", type="integer", description="返回码"),
     *         @OA\Property(property="message", type="string", description="错误信息"),
     *         @OA\Property(property="data", type="object", description="返回数据",
     *             @OA\Property(property="token", type="string", description="用户token"),
     *             @OA\Property(property="type", type="string", description="类型")
     *         ),
     *         @OA\Property(property="timestamp", type="integer", description="服务器响应的时间戳")
     *     ))
     * )
     */
    public function handleLineLogin(Request $request)
    {
        $params = $request->only("line_id", "relation_id", "type");
        try {
            $find = LineUserRelation::where([
                'line_user_id' => $params['line_id'],
                'relation_id' => $params['relation_id'],
                'type' => $params['type'],
            ])->first();
            if ($find) {
                if ($params['type'] == LineUserRelation::TYPE_TEACHER) {
                    $teacher = Teacher::where('id', $params['relation_id'])->first();
                    $type = 'teacher';
                    $token = $teacher->createToken($type, [$type]);
                    return $this->resJson(ErrorCode::SUCCESS, ['token' => $token->accessToken, 'type' => $type]);
                } else if ($params['type'] == LineUserRelation::TYPE_STUDENT) {
                    $student = Student::where('id', $params['relation_id'])->first();
                    $type = 'student';
                    $token = $student->createToken($type, [$type]);
                    return $this->resJson(ErrorCode::SUCCESS, ['token' => $token->accessToken, 'type' => $type]);
                }
            }
            return $this->resJson(ErrorCode::DATA_NO_EXIST);
        } catch (\Throwable $e) {
            return $this->resJson(ErrorCode::ERROR, $e->getMessage());
        }
    }
}

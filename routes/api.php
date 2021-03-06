<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */
Route::post('/login-in', 'Login\\LoginController@loginIn'); // 登录
Route::post('/line/login-in', 'Login\\LoginController@handleLineLogin'); // LINE登录
Route::post('/register', 'Login\\LoginController@registerTeacher'); // 教师注册
Route::get('/teacher/accept', 'Teacher\\TeacherController@acceptInvitation'); // 教师接受邀请
Route::get('/teacher/register-active', 'Teacher\\TeacherController@registerActive'); // 教师账号激活

/**
 * 教师接口
 */
Route::middleware(['auth:teachers', 'scope:teacher'])->prefix('teacher')->group(function () {
    Route::post('login-out', 'Login\\LoginController@loginOut'); // 登出
    Route::get('info', 'Teacher\\IndexController@index'); // 个人信息
    Route::post('bind-line', 'Teacher\\IndexController@bindTeacher'); // line绑定教师

    Route::post('schools', 'Teacher\\TeacherController@applySchool'); // 申请学校
    Route::delete('schools/{id}', 'Teacher\\TeacherController@cancelApplySchool'); // 取消申请学校
    Route::get('schools-apply', 'Teacher\\TeacherController@applySchoolList'); // 申请的学校列表
    Route::get('schools', 'Teacher\\TeacherController@getSchoolList'); // 学校列表

    Route::post('invitation', 'Teacher\\TeacherController@inviteTeacher'); // 邀请教师
    Route::get('teachers-invitation', 'Teacher\\TeacherController@getCanInvitationTeacherList'); // 可以邀请的教师列表
    Route::get('teachers', 'Teacher\\TeacherController@getTeacherList'); // 老师列表

    Route::post('students', 'Teacher\\TeacherController@addStudent'); // 添加学生
    Route::get('students', 'Teacher\\TeacherController@getStudentList'); // 学生列表
    Route::get('students-follow', 'Teacher\\TeacherController@getFollowStudentList'); // 被关注的学生列表

    Route::post('messages', 'Teacher\\TeacherController@sendMessage'); // 发送消息通知
    Route::get('messages', 'Teacher\\TeacherController@getMySendMessage'); // 获取教师发出的消息通知

    Route::get('chat-messages', 'Teacher\\TeacherController@getMessageList'); // 聊天记录

    Route::get('admin-messages', 'Teacher\\TeacherController@getAdminMessageList'); // 管理员推送消息
});

/**
 * 学生接口
 */
Route::middleware(['auth:students', 'scope:student'])->prefix('student')->group(function () {
    Route::post('login-out', 'Login\\LoginController@loginOut'); // 登出
    Route::get('info', 'Student\\IndexController@index'); // 个人信息
    Route::post('bind-line', 'Student\\IndexController@bindStudent'); // line绑定学生

    Route::get('student-info', 'Student\\StudentController@getStudentInfo'); // 学生信息
    Route::get('teachers', 'Student\\StudentController@getTeacherList'); // 教师列表
    Route::post('like', 'Student\\StudentController@like'); // 关注教师
    Route::post('unlike', 'Student\\StudentController@unlike'); // 取消关注教师

    Route::get('messages', 'Student\\StudentController@getTeacherMessageList'); // 获取教师发送的消息通知

    Route::get('chat-messages', 'Student\\StudentController@getMessageList'); // 聊天记录

    Route::get('admin-messages', 'Student\\StudentController@getAdminMessageList'); // 管理员推送消息
});

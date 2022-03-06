<?php

use Encore\Admin\Facades\Admin;
use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix' => config('admin.route.prefix'),
    'namespace' => config('admin.route.namespace'),
    'middleware' => config('admin.route.middleware'),
    'as' => config('admin.route.prefix') . '.',
], function (Router $router) {
    $router->get('/', 'HomeController@index')->name('home');

    $router->resource('schools', SchoolController::class);
    $router->resource('school-applies', SchoolApplyController::class);
    $router->resource('teachers', TeacherController::class);
    $router->resource('students', StudentController::class);
    $router->resource('admin-messages', AdminMessageController::class);

    $router->resource('auth/users', AdminUserController::class)->names('admin.auth.users'); // 重写管理员相关的控制器
    $router->resource('auth/roles', AdminRoleController::class)->names('admin.auth.roles'); // 重写角色相关的控制器
});

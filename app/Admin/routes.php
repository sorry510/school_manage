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
});

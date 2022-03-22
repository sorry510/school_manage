<?php

namespace App\Admin\Controllers;

use App\Models\School;
use App\Models\Student;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class StudentController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '学生';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Student());

        $grid->column('id', __('Id'));
        $grid->column('name', '姓名');
        $grid->column('account', '用户名');
        $grid->column('school.name', '学校');
        $grid->column('online', __('common.online'))->using(Student::LINE_TYPE_TEXTS);
        $grid->column('last_login_time', '最后登录时间');
        $grid->column('last_login_ip', '最后登录ip');
        $grid->column('remark', __('common.remark'));
        $grid->column('created_at', __('common.created_at'));

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Student::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', '姓名');
        $show->field('account', '用户名');
        $show->field('online', __('common.online'))->using(Student::LINE_TYPE_TEXTS);
        $show->field('login_failure', __('Login failure'));
        $show->field('last_login_time', '最后登录时间');
        $show->field('last_login_ip', '最后登录ip');
        $show->field('school.name', '学校');
        $show->field('remark', __('common.remark'));
        $show->field('created_at', __('common.created_at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Student());

        $form->text('name', '姓名')->required();
        $form->text('account', '用户名')
            ->creationRules(['required', "unique:student"], [
                'required' => '用户名必填',
                'unique' => '用户名已经被使用',
            ])
            ->updateRules(['required', "unique:student,account,{{id}}"], [
                'required' => '用户名必填',
                'unique' => '用户名已经被使用',
            ]);
        $form->password('password', __('Password'))->creationRules('required');
        $schools = School::select('id', 'name')->get()->mapWithKeys(function ($item) {
            return [
                $item['id'] => $item['name'],
            ];
        });
        $form->select('school_id', '学校')->options($schools)->required();
        $form->text('remark', __('common.remark'));

        return $form;
    }

    /**
     * 更新操作
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $data = request()->all();
        return $this->form()->update($id, $data);
    }
}

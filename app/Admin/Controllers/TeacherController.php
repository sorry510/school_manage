<?php

namespace App\Admin\Controllers;

use App\Models\Teacher;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class TeacherController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '教师';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Teacher());

        $grid->column('id', __('Id'));
        $grid->column('name', '姓名');
        $grid->column('email', __('common.email'));
        $grid->column('status', __('common.status'))->using(Teacher::STATUS_TEXTS);
        $grid->column('online', __('common.online'))->using(Teacher::LINE_TYPE_TEXTS);
        $grid->column('last_login_time', '最后登录时间');
        $grid->column('last_login_ip', '最后登录ip');
        $grid->column('remark', __('common.remark'));
        $grid->column('created_at', __('common.created_at'));
        $grid->model()->orderBy('id', 'desc');

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
        $show = new Show(Teacher::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', '姓名');
        $show->field('email', __('common.email'));
        $show->field('status', __('common.status'))->using(Teacher::STATUS_TEXTS);
        $show->field('online', __('common.online'))->using(Teacher::LINE_TYPE_TEXTS);
        $show->field('last_login_time', '最后登录时间');
        $show->field('last_login_ip', '最后登录ip');
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
        $form = new Form(new Teacher());

        $form->text('name', '姓名')->required();
        $form->email('email', __('common.email'))->required();
        $form->text('password', '密码')->rules(function ($form) {
            if ($form->isCreating()) {
                return 'required';
            }
            return '';
        });
        $form->select('status', __('common.status'))->options(Teacher::STATUS_TEXTS)->default(Teacher::STATUS_ACTIVE)->required();
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
        // if (empty($data['password'])) {
        //     unset($data['password']);
        // }
        return $this->form()->update($id, $data);
    }
}

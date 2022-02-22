<?php

namespace App\Admin\Controllers;

use App\Models\School;
use App\Models\SchoolTeacher;
use App\Models\Student;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Table;

class SchoolController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '学校';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new School());

        $grid->column('id', __('Id'));
        $grid->column('name', '学校名称');
        $grid->column('teachers', '教师')->expand(function ($model) {
            $teachers = SchoolTeacher::getSchoolTeachers($model->id);
            $data = $teachers->map(function ($teacher) {
                return [
                    $teacher->name,
                    '<a href="#">' . $teacher->email . '</a>',
                    SchoolTeacher::TYPE_TEXTS[$teacher->teacher_type],
                ];
            })->all();
            return new Table(['姓名', '邮箱', '角色'], $data);
        });
        $grid->column('_', '学生')->modal('学生列表', function ($model) {
            $students = Student::select('name', 'account')->where('school_id', $model->id)->get();
            $data = $students->map(function ($item) {
                return [
                    $item->name,
                    '<a href="#">' . $item->account . '</a>',
                ];
            })->all();
            return new Table(['姓名', '用户名'], $data);
        });
        $grid->column('remark', __('common.remark'));
        $grid->column('created_at', __('common.created_at'));
        $grid->model()->orderBy('id', 'desc');
        $grid->disableCreateButton();

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
        $show = new Show(School::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', '学校名称');
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
        $form = new Form(new School());

        $form->text('name', '学校名称');
        $form->text('remark', __('common.remark'));

        return $form;
    }
}

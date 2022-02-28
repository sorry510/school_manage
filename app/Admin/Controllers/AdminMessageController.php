<?php

namespace App\Admin\Controllers;

use App\Models\AdminMessage;
use App\Models\LineUser;
use App\Models\Student;
use App\Models\Teacher;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class AdminMessageController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '推送历史';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new AdminMessage());

        $grid->column('id', __('Id'));
        $grid->column('type', '类型')->using(AdminMessage::TYPE_TEXTS);
        $grid->column('column_not_in_table', '名称')->display(function () {
            if (!$this->teacher_id == 0) {
                return Teacher::where('id', $this->teacher_id)->value('name');
            } else if ($this->student_id) {
                return Student::where('id', $this->student_id)->value('name');
            } else if ($this->line_id != '') {
                return LineUser::where('id', $this->line_id)->value('name');
            } else {
                return '全体';
            }
        });
        $grid->column('content', '内容');
        $grid->column('status', __('common.status'))->using(AdminMessage::STATUS_TEXTS);
        $grid->column('created_at', __('common.created_at'));
        $grid->disableActions(); // 禁用行操作列

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
        $show = new Show(AdminMessage::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('type', '类型')->using(AdminMessage::TYPE_TEXTS);
        $show->field('name_', '名称')->display(function ($model) {
            if (!$model->teacher_id == 0) {
                return Teacher::where('id', $model->teacher_id)->value('name');
            } else if ($model->student_id) {
                return Student::where('id', $model->student_id)->value('name');
            } else if ($model->line_id != '') {
                return LineUser::where('id', $model->line_id)->value('name');
            } else {
                return '-';
            }
        });
        $show->field('content', '内容');
        $show->field('status', __('common.status'))->using(AdminMessage::STATUS_TEXTS);
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
        $form = new Form(new AdminMessage());
        $form->radio('type', '类型')
            ->default(1)
            ->options(AdminMessage::TYPE_TEXTS)
            ->when(AdminMessage::TYPE_ALL, function (Form $form) {
            })
            ->when(AdminMessage::TYPE_TEACHER, function (Form $form) {
                $form->select('teacher_id', '教师')
                    ->options(Teacher::pluck('name', 'id')->toArray());
            })
            ->when(AdminMessage::TYPE_STUDENT, function (Form $form) {
                $form->select('student_id', '学生')
                    ->options(Student::pluck('name', 'id')->toArray());
            })
            ->when(AdminMessage::TYPE_LINE, function (Form $form) {
                $form->select('line_id', 'line用户')
                    ->options(LineUser::pluck('name', 'id')->toArray());
            });
        $form->textarea('content', '内容');

        return $form;
    }

    /**
     * 新增操作
     * @return mixed
     */
    public function store()
    {
        return $this->form()->store();
    }
}

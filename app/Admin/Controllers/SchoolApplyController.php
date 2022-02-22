<?php

namespace App\Admin\Controllers;

use App\Models\SchoolApply;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class SchoolApplyController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '学校申请';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new SchoolApply());

        $grid->column('id', __('Id'));
        $grid->column('name', '申请学校名称');
        $grid->column('teacher.name', '申请教师');
        $grid->column('status', __('common.status'))
            ->using(SchoolApply::STATUS_TEXTS, '成功')
            ->dot(SchoolApply::STATUS_COLORS, 'info');
        $grid->column('created_at', __('common.created_at'));

        $grid->model()->orderBy('id', 'desc');
        $grid->actions(function ($actions) {
            $row = $actions->row;
            if ($row->status !== SchoolApply::STATUS_PENDING) {
                // 去掉删除
                $actions->disableDelete();
                // 去掉编辑
                $actions->disableEdit();
                // 去掉查看
                $actions->disableView();
            }
        });
        // $grid->disableCreateButton();

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
        $show = new Show(SchoolApply::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', '申请学校名称');
        $show->field('teacher.name', '申请教师');
        $show->field('status', __('common.status'))->using(SchoolApply::STATUS_TEXTS, '成功')
            ->dot(SchoolApply::STATUS_COLORS, 'info');
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
        $form = new Form(new SchoolApply());

        // $form->text('name', '申请学校名称');
        // $form->text('teacher_id', '申请教师');
        $form->select('status', __('common.status'))->options(SchoolApply::STATUS_TEXTS);

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
        return $this->form()->update($id);
    }
}

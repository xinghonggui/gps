<?php

namespace App\Admin\Controllers;

use App\Http\Model\ImeiLog;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ImeiLogController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '设备日志';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new ImeiLog());

        $grid->column('id', $this->__LANG('admin.imeilog','Id'));
        $grid->column('imei', $this->__LANG('admin.imeilog','Imei'));
        $grid->column('type', $this->__LANG('admin.imeilog','Type'));
        $grid->column('data', $this->__LANG('admin.imeilog','Data'))->style('width:300px;word-break: break-all;');
        $grid->column('date', $this->__LANG('admin.imeilog','Date'));
        $grid->column('created_at', $this->__LANG('admin.imeilog','Created at'))->sortable();
        $grid->column('updated_at', $this->__LANG('admin.imeilog','Updated at'))->sortable();

        $grid->rows(function ($row) {
            if ($row->number % 2 == 0) {
                $row->style("background-color:#cccccc");
            }
        });
        $grid->filter(function($filter){
            $filter->column(1/2, function ($filter) {
                $filter->equal('imei',$this->__LANG('admin.imeilog','Imei'));
            });
            $filter->column(1/2, function ($filter) {
                $filter->equal('type',$this->__LANG('admin.imeilog','Type'));
                $filter->between('created_at', $this->__LANG('admin.imeilog','Created at'))->datetime();
            });
        });
        $grid->disableRowSelector();
        //禁用行操作
        $grid->disableActions();
        //禁用单选框
        $grid->disableRowSelector();
        //禁用新增按钮
        $grid->disableCreation();
        $grid->actions(function ($actions) {
            // 禁用删除和编辑按钮
            $actions->disableDelete();
        });
        $grid->tools(function ($tools) {
            // 禁用批量删除按钮
            $tools->batch(function ($batch) {
                $batch->disableDelete();

            });
        });

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
        $show = new Show(ImeiLog::findOrFail($id));

        $show->field('id', $this->__LANG('admin.imeilog','Id'));
        $show->field('imei', $this->__LANG('admin.imeilog','Imei'));
        $show->field('type', $this->__LANG('admin.imeilog','Type'));
        $show->field('data', $this->__LANG('admin.imeilog','Data'));
        $show->field('date', $this->__LANG('admin.imeilog','Date'));
        $show->field('created_at', $this->__LANG('admin.imeilog','Created at'));
        $show->field('updated_at', $this->__LANG('admin.imeilog','Updated at'));
        $show->panel()->tools(function ($tools){
            $tools->disableDelete();
            $tools->disableEdit();
        });

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new ImeiLog());

        $form->text('imei', $this->__LANG('admin.imeilog','Imei'));
        $form->text('type', $this->__LANG('admin.imeilog','Type'));
        $form->textarea('data', $this->__LANG('admin.imeilog','Data'));
        $form->datetime('date', $this->__LANG('admin.imeilog','Date'))->default(date('Y-m-d H:i:s'));

        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
            $tools->disableView();
        });
        $form->disableReset();
        $form->disableEditingCheck();
        $form->disableViewCheck();

        return $form;
    }
}

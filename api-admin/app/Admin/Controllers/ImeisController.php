<?php

namespace App\Admin\Controllers;

use App\Http\Model\ImeiLog;
use App\Http\Model\Imeis;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ImeisController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '设备';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Imeis());

        $grid->column('id', $this->__LANG('admin.imeis','Id'));
        $grid->column('imei', $this->__LANG('admin.imeis','Imei'));
        $grid->column('remarks', $this->__LANG('admin.imeis','Remarks'));
        $grid->column('battary',$this->__LANG('admin.imeis','Battary'))->display(function(){
            $data = ImeiLog::query()->where('imei',$this->imei)->where('type',13)->orderBy('created_at','desc')->first();
            $str="None";
            if(!empty($data) && isset($data->data))
            {
                $str = hexdec(mb_substr($data->data,4,2));
            }
            return $str;
        });
        $grid->column('created_at', $this->__LANG('admin.imeis','Created at'))->sortable();
        $grid->column('updated_at', $this->__LANG('admin.imeis','Updated at'))->sortable();

        $grid->rows(function ($row) {
            if ($row->number % 2 == 0) {
                $row->style("background-color:#cccccc");
            }
        });
        $grid->filter(function($filter){
            $filter->column(1/2, function ($filter) {
                $filter->equal('imei',$this->__LANG('admin.imeis','Imei'));
            });
            $filter->column(1/2, function ($filter) {
                $filter->between('created_at', $this->__LANG('admin.imeis','Created at'))->datetime();
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
        $show = new Show(Imeis::findOrFail($id));

        $show->field('id', $this->__LANG('admin.imeis','Id'));
        $show->field('imei', $this->__LANG('admin.imeis','Imei'));
        $show->field('remarks', $this->__LANG('admin.imeis','Remarks'));
        $show->field('created_at', $this->__LANG('admin.imeis','Created at'));
        $show->field('updated_at', $this->__LANG('admin.imeis','Updated at'));
        $show->panel()->tools(function ($tools){
            $tools->disableDelete();
            //$tools->disableEdit();
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
        $form = new Form(new Imeis());

        $form->text('imei', $this->__LANG('admin.imeis','Imei'));
        $form->text('remarks', $this->__LANG('admin.imeis','Remarks'));

        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
            $tools->disableView();
        });
        $form->disableReset();
        //$form->disableEditingCheck();
        $form->disableViewCheck();

        return $form;
    }
}

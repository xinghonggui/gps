<?php

namespace App\Admin\Controllers;

use App\Http\Model\GpsData;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class GpsDataController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '定位数据';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new GpsData());
        
        $grid->column('id', $this->__LANG('admin.gpsdata','Id'));
        $grid->column('imei', $this->__LANG('admin.gpsdata','Imei'));
        $grid->column('method', $this->__LANG('admin.gpsdata','Method'));
        $grid->column('datetime', $this->__LANG('admin.gpsdata','Datetime'));
        $grid->column('valid', $this->__LANG('admin.gpsdata','Valid'));
        $grid->column('nb_sat', $this->__LANG('admin.gpsdata','Nb sat'));
        $grid->column('latitude', $this->__LANG('admin.gpsdata','Latitude'));
        $grid->column('longitude', $this->__LANG('admin.gpsdata','Longitude'));
        $grid->column('accuracy', $this->__LANG('admin.gpsdata','Accuracy'));
        $grid->column('speed', $this->__LANG('admin.gpsdata','Speed'));
        $grid->column('heading', $this->__LANG('admin.gpsdata','Heading'));
        $grid->column('created_at', $this->__LANG('admin.gpsdata','Created at'))->sortable();
        $grid->column('updated_at', $this->__LANG('admin.gpsdata','Updated at'))->sortable();


        $grid->rows(function ($row) {
            if ($row->number % 2 == 0) {
                $row->style("background-color:#cccccc");
            }
        });
        $grid->filter(function($filter){
            $filter->column(1/2, function ($filter) {
                $filter->equal('imei',$this->__LANG('admin.gpsdata','Imei'));
            });
            $filter->column(1/2, function ($filter) {
                $filter->between('created_at', $this->__LANG('admin.gpsdata','Created at'))->datetime();
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
        $show = new Show(GpsData::findOrFail($id));

        $show->field('id', $this->__LANG('admin.gpsdata','Id'));
        $show->field('imei', $this->__LANG('admin.gpsdata','Imei'));
        $show->field('method', $this->__LANG('admin.gpsdata','Method'));
        $show->field('datetime', $this->__LANG('admin.gpsdata','Datetime'));
        $show->field('valid', $this->__LANG('admin.gpsdata','Valid'));
        $show->field('nb_sat', $this->__LANG('admin.gpsdata','Nb sat'));
        $show->field('latitude', $this->__LANG('admin.gpsdata','Latitude'));
        $show->field('longitude', $this->__LANG('admin.gpsdata','Longitude'));
        $show->field('accuracy', $this->__LANG('admin.gpsdata','Accuracy'));
        $show->field('speed', $this->__LANG('admin.gpsdata','Speed'));
        $show->field('heading', $this->__LANG('admin.gpsdata','Heading'));
        $show->field('created_at', $this->__LANG('admin.gpsdata','Created at'));
        $show->field('updated_at', $this->__LANG('admin.gpsdata','Updated at'));
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
        $form = new Form(new GpsData());

        $form->text('imei', $this->__LANG('admin.gpsdata','Imei'));
        $form->text('method', $this->__LANG('admin.gpsdata','Method'));
        $form->datetime('datetime', $this->__LANG('admin.gpsdata','Datetime'))->default(date('Y-m-d H:i:s'));
        $form->number('valid', $this->__LANG('admin.gpsdata','Valid'));
        $form->number('nb_sat', $this->__LANG('admin.gpsdata','Nb sat'));
        $form->decimal('latitude', $this->__LANG('admin.gpsdata','Latitude'));
        $form->decimal('longitude', $this->__LANG('admin.gpsdata','Longitude'));
        $form->decimal('accuracy', $this->__LANG('admin.gpsdata','Accuracy'));
        $form->number('speed', $this->__LANG('admin.gpsdata','Speed'));
        $form->number('heading', $this->__LANG('admin.gpsdata','Heading'));

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

<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use App\Models\UserLevel\UserLevel;

class LevelController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {
           
            $content->header('等级管理');
            $content->description(' ');

            $content->body($this->grid());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(UserLevel::class, function (Grid $grid) {

            $grid->model();
            $grid->id('ID');
            $grid->name('等级名称');
            $grid->profit('通关奖励')->help('通过8关,奖励金额');
            $grid->disableCreateButton();
            $grid->disableFilter();
            $grid->disableActions();
            $grid->disableRowSelector();
            $grid->disableExport();
            $grid->disableColumnSelector();
            $grid->disablePagination();
        });
    }


}

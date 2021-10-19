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
use App\Models\Rank\Rank;

class RankController extends Controller
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
           
            $content->header('奖励配置');
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
        return Admin::grid(Rank::class, function (Grid $grid) {

            $grid->model();
            $grid->id('ID');
            $grid->name('关数');
            $grid->price_1('投注')->help('50档')->setAttributes(['style' => 'color:red;']);
            $grid->profit_1('返利')->help('50档')->setAttributes(['style' => 'color:red;']);
            $grid->attach_1('附加')->help('50档')->setAttributes(['style' => 'color:red;']);
            $grid->price_2('投注')->help('30档')->setAttributes(['style' => 'color:green;']);
            $grid->profit_2('返利')->help('30档')->setAttributes(['style' => 'color:green;']);
            $grid->attach_2('附加')->help('30档')->setAttributes(['style' => 'color:green;']);
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

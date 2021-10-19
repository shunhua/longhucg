<?php

namespace App\Admin\Controllers;

use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Row;
use Encore\Admin\Layout\Content;
use Closure;

trait FilterQuery 
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function filterGrid()
    {
        return Admin::grid(self::MODEL, function (Grid $grid) {

        });
    }

    public function condition(Content $content, Closure $callable)
    {
        $content->row(function (Row $row) use ($callable) {
            
            $grid = $this->filterGrid();
            $grid->filter($this->filter());
            $conditions = $grid->getFilter()->conditions();

            $model = self::MODEL;
            $model = new $model;

            foreach ($conditions as $value) {
                $model = call_user_func_array([$model, key($value)], current($value));
            }

            call_user_func($callable, $model, $row);
        });
    }

}

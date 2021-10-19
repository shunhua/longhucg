<?php

use App\Admin\Extensions\WangEditor;
use Encore\Admin\Form;
use Encore\Admin\Grid\Column;
use App\Admin\Extensions\Column\UrlWrapper;
use App\Admin\Extensions\UserLine;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Grid;

/**
 * Laravel-admin - admin builder based on Laravel.
 * @author z-song <https://github.com/z-song>
 *
 * Bootstraper for Admin.
 *
 * Here you can remove builtin form field:
 * Encore\Admin\Form::forget(['map', 'editor']);
 *
 * Or extend custom form field:
 * Encore\Admin\Form::extend('php', PHPEditor::class);
 *
 * Or require js and css assets:
 * Admin::css('/packages/prettydocs/css/styles.css');
 * Admin::js('/packages/prettydocs/js/main.js');
 *
 */

Encore\Admin\Form::forget(['map', 'editor']);

Form::extend('editor', WangEditor::class);

Admin::js('/vendor/clipboard/dist/clipboard.min.js');
Admin::js('/vendor/chartjs/Chart.min.js');

Column::extend('urlWrapper', UrlWrapper::class);

Column::extend('prependIcon', function ($value, $icon) {
    return "<span style='color: #999;'><i class='fa fa-$icon'></i>  $value</span>";
});

// 表单初始化
Form::init(function (Form $form) {

    $form->disableEditingCheck();

    $form->disableCreatingCheck();

    $form->disableViewCheck();

    $form->tools(function (Form\Tools $tools) {
        $tools->disableDelete();
        $tools->disableView();
        // $tools->disableList();
    });
});

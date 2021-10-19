<?php
namespace App\Admin\Extensions\Tools;
use Encore\Admin\Admin;
use Encore\Admin\Grid\Tools\AbstractTool;
use Illuminate\Support\Facades\Request;
use App\Models\User\User;

class Diff extends AbstractTool
{
    public function script()
    {
        $url = Request::fullUrlWithQuery(['diff' => '_gender_']);

        return <<<EOT

$('input:radio.user-diff').change(function () {

    var url = "$url".replace('_gender_', $(this).val());

    $.pjax({container:'#pjax-container', url: url });

});

EOT;
    }

    public function render()
    {
        Admin::script($this->script());

        $options = User::$analogText;

        return view('admin.tools.diff', compact('options'));
    }
}
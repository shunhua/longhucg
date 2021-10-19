<?php

namespace App\Admin\Extensions\Column;

use Encore\Admin\Facades\Admin;
use Encore\Admin\Grid\Displayers\AbstractDisplayer;
use App\Models\AdminRole\AdminRole;

class UrlWrapper extends AbstractDisplayer
{
    protected function script()
    {
        return <<<EOT

$('.grid-qrcode').popover({
    title: "代理推广二维码",
    html: true,
    trigger: 'focus'
});

new Clipboard('.clipboard');

$('.clipboard').tooltip({
  trigger: 'click',
  placement: 'bottom'
}).mouseout(function (e) {
    $(this).tooltip('hide');
});

EOT;
    }

    public function display()
    {
        if (in_array($this->value, [0,7,8])) return '';

        Admin::script($this->script());
        $url = env('APP_URL'). '/register.html?invite=' . $this->row->code;
        //$url = url('member-register') . '?id=' . $this->row->code;
        $qrcode = "<img src='https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={$url}' style='height: 150px;width: 150px;'/>";

        return <<<EOT

<div class="input-group" style="width:250px;">
  <input type="text" id="grid-homepage-{$this->getKey()}" class="form-control input-sm" value="{$url}" />
  <span class="input-group-btn">
    <button class="btn btn-default btn-sm clipboard" data-clipboard-target="#grid-homepage-{$this->getKey()}" title="Copied!">
        <i class="fa fa-clipboard"></i>
    </button>
    <a class="btn btn-default btn-sm grid-qrcode" data-content="$qrcode" data-toggle='popover' tabindex='0'>
        <i class="fa fa-qrcode"></i>
    </a>
  </span>
</div>

EOT;
    }
}
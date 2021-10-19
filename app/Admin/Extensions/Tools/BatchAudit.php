<?php

namespace App\Admin\Extensions\Tools;

use Encore\Admin\Admin;
use Illuminate\Support\Facades\Request;
use Encore\Admin\Grid\Tools\BatchAction;

class BatchAudit extends BatchAction
{
    /**
     * 1:通过 2:驳回
     *
     * @var int
     */
    protected $action;

    public function __construct($action = 1)
    {
        $this->action = $action;
    }

    public function script()
    {
        return <<<EOT

$('{$this->getElementClass()}').on('click', function() {

    $.ajax({
        method: 'post',
        url: '/admin/shop/atm_batch_audit',
        data: {
            _token:LA.token,
            ids: selectedRows(),
            action: {$this->action}
        },
        success: function () {
            $.pjax.reload('#pjax-container');
            toastr.success('操作成功');
        }
    });
});

EOT;
    }

}
<?php

namespace App\Models\Atm;

use Illuminate\Database\Eloquent\Model;
use App\Models\Atm\Traits\Relationship\AtmRelationship;
use App\Models\User\Traits\Scope\DiffScope;

class Atm extends Model
{
    use AtmRelationship,
        DiffScope;

    /**
     * 状态
     */
    CONST STATE_PASS = 1;
    CONST STATE_REJECT = 0;
    CONST STATE_NORMAL = 2;

    CONST STATE_TEXT = [
        0 => '驳回',
        1 => '通过',
        2 => '等待审核',
    ]; 
     
    /**
     * 代付银行状态
     */
    CONST PAY_PASS = 1;
    CONST PAY_DEFAULT = 0;
    CONST PAY_FAIL = 2;

    CONST PAY_TEXT = [
        0 => '未处理',
        1 => '代付成功',
        2 => '代付处理中',
    ];  

}


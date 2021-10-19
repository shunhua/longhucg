<?php

namespace App\Models\AccountDetail;

use Illuminate\Database\Eloquent\Model;
use App\Models\AccountDetail\Traits\Relationship\AccountDetailRelationship;
use App\Models\AccountDetail\Traits\Scope\AccountDetailScope;
use App\Models\User\Traits\Scope\DiffScope;

class AccountDetail extends Model
{
    use AccountDetailRelationship,
        AccountDetailScope,
        DiffScope;

    /**
     * 增加 或 减少 类型
     */
    CONST ACCOUNT_INCREASE = 1;
    CONST ACCOUNT_DECREASE = 2;

    CONST ACCOUNT = [
        1 => '收入',
        2 => '支出',
    ];  

    /**
     * 类型
     */
    CONST RELATIONSHIP_TYPE_ORDER = 1;
    CONST RELATIONSHIP_TYPE_PROFIT = 2;
    CONST RELATIONSHIP_TYPE_ATM= 3;
    CONST RELATIONSHIP_TYPE_RECHARGE= 4;
    CONST RELATIONSHIP_TYPE_INCREASE= 5;
    CONST RELATIONSHIP_TYPE_REDUCE = 6;
    CONST RELATIONSHIP_TYPE_ATM_REJECT = 7;


    CONST RELATIONSHIP_TYPE = [
        1 => '投注下单',
        2 => '彩票返利',
        3 => '提现',
        4 => '充值',
        5 => '系统充值',
        6 => '系统扣除',
        7 => '提现驳回',
    ]; 

}


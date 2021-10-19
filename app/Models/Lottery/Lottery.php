<?php

namespace App\Models\Lottery;

use Illuminate\Database\Eloquent\Model;
use App\Models\Lottery\Traits\Relationship\LotteryRelationship;
use App\Models\Lottery\Traits\Scope\LotteryScope;
use App\Models\User\Traits\Scope\DiffScope;

class Lottery extends Model
{
    use LotteryRelationship,
        LotteryScope,
        DiffScope; 

    /**
     * status
     */
    CONST STATUSING = 1;
    CONST STATUS_END = 2;

    CONST STATUS_TEXT = [
        1 => '进行中',
        2 => '结束',
    ];  

    /**
     * 可以被集体附值的表的字段
     *
     * @var string
     */
    protected $guarded  = [];

}


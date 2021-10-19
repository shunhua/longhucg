<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;
use App\Models\Order\Traits\Relationship\OrderRelationship;
use App\Models\Order\Traits\Scope\OrderScope;
use App\Models\Order\Traits\Attribute\OrderAttribute;
use App\Models\User\Traits\Scope\DiffScope;

class Order extends Model
{
    use OrderRelationship,
        OrderAttribute,
        OrderScope,
        DiffScope;
        
    /**
     * 大小双单
     */
    CONST ORDER_ONE = 1;
    CONST ORDER_TWO = 2;
    CONST ORDER_THREE = 3;
    CONST ORDER_FOUR = 4;
    CONST ORDER_FIVE = 5;
    CONST ORDER_SIX = 6;
    CONST ORDER_SEVEN = 7;
    CONST ORDER_EIGHT = 8;
    CONST ORDER_NINE = 9;
    CONST ORDER_TEN = 10;

    CONST ORDER_TYPE = [
        1 => '万千',
        2 => '万百',
        3 => '万十',
        4 => '万个',
        5 => '千百',
        6 => '千十',
        7 => '千个',
        8 => '百十',
        9 => '百个',
        10 => '十个'
    ];

    CONST TYPE = [
        '万千' => self::ORDER_ONE,
        '万百' => self::ORDER_TWO,
        '万十' => self::ORDER_THREE,
        '万个' => self::ORDER_FOUR,
        '千百' => self::ORDER_FIVE,
        '千十' => self::ORDER_SIX,
        '千个' => self::ORDER_SEVEN,
        '百十' => self::ORDER_EIGHT,
        '百个' => self::ORDER_NINE,
        '十个' => self::ORDER_TEN
    ];

    /**
     * 龙虎类型
    */
    CONST TYPE_LONG = 1;
    CONST TYPE_HU = 2;
    CONST LH_TYPE = [
        1 => '龙',
        2 => '虎'
    ];
    CONST LHTYPE = [
        '龙' => self::TYPE_LONG,
        '虎' => self::TYPE_HU
    ];

    /**
     * 是否结算
    */
    CONST SETTLEMENT = 1;
    CONST SETTLEMENT_NO = 2;

    CONST SETTLEMENT_TEXT = [
        1 => '是',
        2 => '否',
    ]; 

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
     * 输、赢、和
     */
    CONST WIN = 1;
    CONST WIN_NO = 2;
    CONST WIN_DRAW = 3;

    CONST WIN_TEXT = [
        0 => '进行中',
        1 => '赢',
        2 => '输',
        3 => '和',
    ];  
    /**
     * 第几关
     */
    CONST ORDER_BARRIER = [
        1 => '第一关',
        2 => '第二关',
        3 => '第三关',
        4 => '第四关',
        5 => '第五关',
        6 => '第六关',
        7 => '第七关',
        8 => '第八关'
    ];   
    
    /**
     * 可以被集体附值的表的字段
     *
     * @var string
     */
    protected $guarded  = [];

}


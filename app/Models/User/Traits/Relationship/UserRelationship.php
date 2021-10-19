<?php

namespace App\Models\User\Traits\Relationship;

use App\Models\AdminUser\AdminUser;
use App\Models\Order\Order;
use App\Models\AccountDetail\AccountDetail;
use App\Models\UserLevel\UserLevel;
use App\Models\Notice\Notice;
use App\Models\Card\Card;

trait UserRelationship
{

    public function notice()
    {
        return $this->hasMany(Notice::class);
    }

     /**
     * 订单
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * 资金明细
     */
    public function accounts()
    {
        return $this->hasMany(AccountDetail::class);
    }

    /**
     * 绑卡 
     */
    public function cards()
    {
        return $this->hasMany(Card::class);
    }

    /**
     * 会员等级
     */
    public function level()
    {
        return $this->belongsTo(UserLevel::class);
    }
    
}

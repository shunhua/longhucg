<?php

namespace App\Models\Order\Traits\Relationship;

use App\Models\User\User;
use App\Models\Lottery\Lottery;

trait OrderRelationship
{
 	/**
     * 用户
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 期数
     */
    public function lottery()
    {
        return $this->belongsTo(Lottery::class);
    }

}

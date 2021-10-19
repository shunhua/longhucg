<?php

namespace App\Models\Recharge\Traits\Relationship;

use App\Models\User\User;

/**
 * Class RechargeRelationship.
 */
trait RechargeRelationship
{
    /**
     * 用户
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}

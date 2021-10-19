<?php

namespace App\Models\Card\Traits\Relationship;

use App\Models\User\User;

trait CardRelationship
{
 	/**
     * 用户
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

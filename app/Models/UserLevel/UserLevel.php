<?php

namespace App\Models\UserLevel;

use Illuminate\Database\Eloquent\Model;
use App\Models\UserLevel\Traits\Attribute\UserLevelAttribute;
use App\Models\UserLevel\Traits\Scope\UserLevelScope;

class UserLevel extends Model
{
    use UserLevelAttribute,
        UserLevelScope;

    /**
     * 是否使用 
     */
    CONST LEVEL_ORDINARY = 1;
    CONST LEVEL_EXPER = 2;

}


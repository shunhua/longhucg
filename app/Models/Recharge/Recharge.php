<?php

namespace App\Models\Recharge;

use Illuminate\Database\Eloquent\Model;
use App\Models\Recharge\Traits\Relationship\RechargeRelationship;
use App\Models\User\Traits\Scope\DiffScope;

class Recharge extends Model
{
    use RechargeRelationship,
        DiffScope;
}


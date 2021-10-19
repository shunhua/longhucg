<?php

namespace App\Models\Rank;

use Illuminate\Database\Eloquent\Model;
use App\Models\Rank\Traits\Attribute\RankAttribute;
use App\Models\Rank\Traits\Scope\RankScope;

class Rank extends Model
{
    use RankAttribute,
        RankScope;

}


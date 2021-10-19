<?php

namespace App\Models\Card;

use Illuminate\Database\Eloquent\Model;
use App\Models\Card\Traits\Relationship\CardRelationship;

class Card extends Model
{
  use CardRelationship;  

	/**
     * 可以被集体附值的表的字段
     */
   protected $guarded  = [];

}


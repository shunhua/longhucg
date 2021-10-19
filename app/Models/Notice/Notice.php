<?php

namespace App\Models\Notice;

use Illuminate\Database\Eloquent\Model;
use App\Models\User\User;

class Notice extends Model
{
	/**
     * 可以被集体附值的表的字段
     */
   protected $guarded  = [];


    /**
     * 用户
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }


}


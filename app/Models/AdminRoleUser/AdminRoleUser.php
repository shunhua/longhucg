<?php

namespace App\Models\AdminRoleUser;

use Illuminate\Database\Eloquent\Model;

class AdminRoleUser extends Model
{
    protected $fillable = ['role_id', 'user_id'];
}


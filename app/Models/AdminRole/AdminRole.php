<?php

namespace App\Models\AdminRole;

use Illuminate\Database\Eloquent\Model;

class AdminRole extends Model
{
    /**
     * 角色
     */
    CONST SLUG_OPERATION = 'operation';
    CONST SLUG_MEMBER = 'member';
    CONST SLUG_AGENT = 'agent';
    CONST SLUG_STAFF = 'staff';
    CONST SLUG_OPERATION_ID = 2;
    CONST SLUG_MEMBER_ID = 3;
    CONST SLUG_AGENT_ID = 4;
    CONST SLUG_STAFF_ID = 5;
    
    CONST SLUG_TEXT = [
        2 => '运营中心',
        3 => '会员中心',
        4 => '代理中心',
        5 => '员工中心',
    ];

    /**
     * 获取角色下级ID
     */
    public static function childrenId($id)
    {
        $id = is_int($id) ? explode(' ', $id) : $id;

        $data = self::whereIn('parent_id', $id)->pluck('id')->toArray();
        if (! empty($data)) $data = array_merge($data, self::childrenId($data));
        return $data;
    }

    /**
     * 判断两个代理相差级别
     */
    public static function diffeeGrade($parent_id, $id, $num = 1)
    {
        $role = self::select('parent_id')->find($id);
        if ($role) {
            if ($role->parent_id == $parent_id) {
                return $num;
            }
            return self::diffeeGrade($parent_id, $role->parent_id, ++$num);
        }
        return false;
    }

}


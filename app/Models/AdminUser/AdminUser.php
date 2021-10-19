<?php

namespace App\Models\AdminUser;

use Illuminate\Database\Eloquent\Model;
use App\Models\User\User;

class AdminUser extends Model
{
    /**
     * 获取管理下级管理ID（包含自己）
     */
    public static function ownChildrenId($id)
    {
        $childrenId = self::childrenId($id);
        return array_merge($childrenId, [$id]);
    }

    /**
     * 获取管理下级管理ID
     */
    public static function childrenId($id)
    {
        $id = is_int($id) ? explode(' ', $id) : $id;

        $data = self::select('id')->whereIn('parent_id', $id)->pluck('id')->toArray();
        if (! empty($data)) $data = array_merge($data, self::childrenId($data));
        return $data;
    }

    /**
     * 获取管理下的所有会员ID
     */
    public static function childrenMemberId($id)
    {
        $childrenId = self::childrenId($id);
        array_push($childrenId, $id);
        $userId = User::whereIn('parent_admin', $childrenId)->pluck('id')->toArray();
        return $userId;
    }

    /**
     * 管理是否是下级管理
     */
    public static function isChildren($parent_id, $id)
    {
        return in_array($id, self::childrenId($parent_id));
    }

    /**
     * 获取后台管理会员信息[代理比例]
     * @bsh
     */
    public static function adminInfo($id)
    {
        $royalty_ratio = self::find($id)->royalty_ratio;
        return $royalty_ratio;
    }

     /**
     * 获取管理下级的最大提成比例 bsh
     */
    public static function childrenMaxratio($admin_id)
    {
        $parent = self::select('royalty_ratio')->where('parent_id', $admin_id)->orderBy('royalty_ratio', 'desc')->first();
        $data = !empty($parent) ?  $parent['royalty_ratio'] : 0;
        return $data;
    }


    /**
     * 通过会员获取上级的一条线管理 [包括自己]
     */
    public static function parentAdmins($admin_id)
    {
        //首先定义一个静态字符串常量用来保存结果  
        $parentMembers = $admin_id.',';
        
        $parentMembers.=self::parentIdAdmin($admin_id);

        return $parentMembers;
    }


    /**
     * 会员一条线
     *
     * @param  int $user_id 
     * @return int
     */
    public static function parentIdAdmin($user_id,&$parentAdmin_strs='')
    {
      $parent_id = self::find($user_id)->parent_id;
      $parentids = array(0,1); //主要是限制第一个模拟不拿提成          
      if (!in_array($parent_id, $parentids) ) {
        $parentAdmin_strs .= $parent_id.',';
        self::parentIdAdmin($parent_id,$parentAdmin_strs); 
       }
      return $parentAdmin_strs;
    }

}


<?php

namespace App\Models\User;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\User\Traits\UserAccess;
use App\Models\User\Traits\Relationship\UserRelationship;
use App\Models\User\Traits\Attribute\UserAttribute;
use App\Models\User\Traits\Scope\DiffScope;

class User extends Authenticatable
{
    use HasApiTokens,
        Notifiable,
        UserAccess,
        UserRelationship,
        UserAttribute,
        DiffScope;

    /**
     * 冻结
    */
    CONST STATE_FREEZE = 1;
    CONST STATE_NO_FREEZE = 0;

    /**
     * 模拟
    */
    CONST STATE_ANALOG = 1;
    CONST STATE_NO_ANALOG = 0;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'mobile', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function findForPassport($username) 
    {
        return $this->where('mobile', $username)->first();
    }

    /**
     * 余额是否足够
     *
     * @param float $price
     * @return bool
     */
    public function gteBalance($price)
    {
        return $this->balance >= $price;
    }

    /**
     * 获取会员下级ID（包含自己）
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
     * 通过会员获取上级的一条线管理
     */
    public static function parentUserLine($user_id)
    {
        //首先定义一个静态字符串常量用来保存结果  
        $parentMembers = ''; 
        
        $parentMembers.=self::parentIdUser($user_id);

        return $parentMembers;
    }

    /**
     * 通过会员获取上级的一条线管理 [包括自己]
     */
    public static function parentUsers($user_id)
    {
        //首先定义一个静态字符串常量用来保存结果  
        $parentMembers = $user_id.',';
        
        $parentMembers.=self::parentIdUser($user_id);

        return $parentMembers;
    }


    /**
     * 会员一条线
     *
     * @param  int $user_id 
     * @return int
     */
    public static function parentIdUser($user_id,&$parentAdmin_strs='')
    {
      $parent_id = self::find($user_id)->parent_id;
      $parentids = array(0,1); //主要是限制第一个模拟不拿提成          
      if (!in_array($parent_id, $parentids) ) {
        $parentAdmin_strs .= $parent_id.',';
        self::parentIdUser($parent_id,$parentAdmin_strs); 
       }
      return $parentAdmin_strs;
    }

    /**
     * 获取会员下级所有关联会员ID
     */
    public static function memberChildrenId($id,&$id_strs=array())
    {
        $id = is_int($id) ? explode(' ', $id) : $id;
        $parent = self::select('id')->whereIn('parent_id', $id)->get()->toArray();
        $data = !empty($parent) ? array_column($parent, 'id') : [];
        if (! empty($data)) {
            $id_strs[]= $data;
            self::memberChildrenId($data,$id_strs);
        }
       
        return $id_strs;
    }

    /**
     * 统计下级总人数
     */
    public static function memberChildrenCount($id,&$sum=null)
    {
        $id = is_int($id) ? explode(' ', $id) : $id;
        $count = self::whereIn('parent_id', $id)->count();
        $parent = self::select('id')->whereIn('parent_id', $id)->get()->toArray();
        $data = !empty($parent) ? array_column($parent, 'id') : [];
        if (! empty($data)) {
            $sum += $count;
            self::memberChildrenCount($data,$sum);
        }
       
        return !empty($sum) ? $sum : 0;
    }

    /**
     * 是否模拟
     */
    public static function isAn($user_id)
    {
        $analog = self::find($user_id)->analog;
        return $analog ==1 ? true : false;
    }

}

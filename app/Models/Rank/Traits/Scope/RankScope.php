<?php

namespace App\Models\Rank\Traits\Scope;

/**
 * Class RankScope.
 */
trait RankScope
{
	/**
     * 下一关
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNext($query, $id)
    {
        return $query->where('id', $id+1);
    }

}

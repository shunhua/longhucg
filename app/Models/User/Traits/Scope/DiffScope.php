<?php

namespace App\Models\User\Traits\Scope;

/**
 * Class UserScope.
 */
trait DiffScope
{
    /**
     * 标识
     */
    public static $noAnalog = 0;
    public static $analog = 1;

    public static $analogText = [
        '0' => '实盘',
        '1' => '模拟'
    ];

    /**
     * 区分模拟盘
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDiff($query, $value)
    {
        return $query->where('analog', $value ?: self::$noAnalog);
    }

    /**
     * 模拟
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAnalog($query)
    {
        return $query->where('analog', self::$analog);
    }

    /**
     * 非模拟
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNoAnalog($query)
    {
        return $query->where('analog', self::$noAnalog);
    }
}

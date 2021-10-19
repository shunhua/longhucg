<?php

namespace App\Models\Config;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
	/**
     * 数据表名
     *
     * @var string
     */
    protected $table = 'config';
    
	/**
     * 可以被集体附值的表的字段
     *
     * @var string
     */
    protected $fillable = array('name', 'value');

    public $timestamps = false;

    /**
     * 获取配置的值
     *
     * @param string $calls
     * @return mixed
     */
    public static function getValue($calls)
    {
        static $configs;
        if ($configs) return $configs[$calls];
        $configs = self::pluck('value', 'calls')->all();
        return $configs[$calls];
    }
}


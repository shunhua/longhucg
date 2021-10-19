<?php

namespace App\Models\Ad;

use Illuminate\Database\Eloquent\Model;
use Storage;

class Ad extends Model
{
    /**
     * 获取配置的值
     *
     * @param string $calls
     * @return mixed
     */
    public static function getValue($calls)
    {
        static $ad = [];
        if (isset($ad[$calls])) return $ad[$calls];
        $value = self::where('mark', $calls)->value('img');
        return $ad[$calls] = self::HandleValue($value);
    }

    private static function HandleValue($value)
    {
        if ($value) return Storage::disk(config('admin.upload.disk'))->url($value);
        return null;
    } 
}


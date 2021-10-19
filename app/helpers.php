<?php

/**
 * Global helpers file with misc functions.
 */
if (! function_exists('_config')) {
    /**
     * Helper to grab the application name.
     *
     * @return mixed
     */
    function _config($calls)
    {
        return App\Models\Config\Config::getValue($calls);
    }
}

/**
 * Global helpers file with misc functions.
 */
if (! function_exists('_ad')) {
    /**
     * Helper to grab the application name.
     *
     * @return mixed
     */
    function _ad($calls)
    {
        return App\Models\Ad\Ad::getValue($calls);
    }
}

/**
 * Global helpers file with misc functions.
 */
if (! function_exists('_type')) {
    /**
     * Helper to grab the application name.
     *
     * @return mixed
     */
    function _type($type, $flug=1)
    {
        if ($flug==1) {
            return App\Models\AccountDetail\AccountDetail::RELATIONSHIP_TYPE[$type];
        }else{
            return App\Models\AccountDetail\AccountDetail::ACCOUNT[$type];
        }
        
    }
}

/**
 * Global helpers file with misc functions.
 */
if (! function_exists('trade_no')) {
    /**
     * Helper to grab the application name.
     *
     * @return mixed
     */
    function trade_no($prefix = '')
    {
        $y_code = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        $trade_no = $y_code[intval(date('y')) % 10]
                    . strtoupper(dechex(date('m')))
                    . date('d') . substr(time(), -5)
                    . substr(microtime(), 2, 5)
                    . sprintf('%02d', rand(0, 99));
        return $prefix.$trade_no;
    }
}

if (! function_exists('generatePassword')) {
    function generatePassword( $length = 8 ) {
     // 密码字符集，可任意添加你需要的字符
     $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
     
     $password = '';
     for ( $i = 0; $i < $length; $i++ ) 
     {
      // 这里提供两种字符获取方式
      // 第一种是使用 substr 截取$chars中的任意一位字符；
      // 第二种是取字符数组 $chars 的任意元素
      // $password .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
      $password .= $chars[ mt_rand(0, strlen($chars) - 1) ];
     }
     
     return $password;
    }
}  

/**
 * 加载静态资源
 *
 * @param string $file 所要加载的资源
 */
if ( ! function_exists('loadStatic'))
{
    function loadStatic($file)
    {
        $realFile = public_path().$file;
        if( ! file_exists($realFile)) return '';
        $filemtime = filemtime($realFile);
        return Request::root().$file.'?v='.$filemtime;
    }
}

/**
 * 返回json
 *
 * @param string $msg 返回的消息
 * @param boolean $status 是否成功
 */
if( ! function_exists('responseJson'))
{
    function responseJson($msg, $status = false, $data = [])
    {
        $status = $status ? 'success' : 'error';
        $arr = array('result' => $status, 'message' => $msg, 'data' => $data);
        return Response::json($arr);
    }
}

/**
* 对银行卡号进行掩码处理
* 掩码规则头4位,末尾余数位不变，中间4的整数倍字符用星号替换，并且用每隔4位空格隔开
* @author 晓风<215628355@qq.com>
* @param  string $bankCardNo 银行卡号
* @return string             掩码后的银行卡号
*/
if( ! function_exists('formatBankCardNo'))
{
    function formatBankCardNo($bankCardNo){
        //每隔4位分割为数组
        $split = str_split($bankCardNo,4);    
        //头和尾保留，其他部分替换为星号       
        $split = array_fill(1,count($split) - 2,"****") + $split;
        ksort($split);
        //合并
        return implode(" ",$split);
    }
}



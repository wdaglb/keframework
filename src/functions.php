<?php
/**
 * Name: functions.php
 * User: King east
 * Site: http://cms.iydou.cn/
 */
/**
 * 获取当前域名
 * @return string
 */
function get_domain(){
    $host = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
    return $host;
}

/**
 * 获取客户端IP
 * @param bool $type
 * @return mixed
 */
function get_client_ip($type=false){
    $type       =  $type ? 1 : 0;
    static $ip  =   NULL;
    if ($ip !== NULL) return $ip[$type];
    if(isset($_SERVER['HTTP_X_REAL_IP'])){//nginx 代理模式下，获取客户端真实IP
        $ip=$_SERVER['HTTP_X_REAL_IP'];
    }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {//客户端的ip
        $ip     =   $_SERVER['HTTP_CLIENT_IP'];
    }elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {//浏览当前页面的用户计算机的网关
        $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $pos    =   array_search('unknown',$arr);
        if(false !== $pos) unset($arr[$pos]);
        $ip     =   trim($arr[0]);
    }elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip     =   $_SERVER['REMOTE_ADDR'];//浏览当前页面的用户计算机的ip地址
    }else{
        $ip=$_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u",ip2long($ip));
    $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}


/**
 * pascal转下划线命名
 * @param  string $value 需要转换的字符串
 * @return string        转换后的字符
 */
function pascal_to_line($value='')
{
    $exp=explode('\\',$value);
    $table=end($exp);
    $table=preg_replace_callback('/([a-z])([A-Z])/',function($to){
        return "{$to[1]}_{$to[2]}";
    }, $table);
    return strtolower($table);
}

function line_to_pascal($value)
{
    $value=preg_replace_callback('/_([a-z]+)/',function($to){
        return ucwords($to[1]);
    },$value);
    return $value;
}
<?php
/**
 * Name: functions.php
 * User: King east
 * Site: http://cms.iydou.cn/
 */
function get_domain(){
    $host = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
    return $host;
}
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
 * 返回数组维度
 * @param $arr
 * @return mixed
 */
function arrayLevel($arr){
    $al = array(0);
    function aL($arr,&$al,$level=0){
        if(is_array($arr)){
            $level++;
            $al[] = $level;
            foreach($arr as $v){
                aL($v,$al,$level);
            }
        }
    }
    aL($arr,$al);
    return max($al);
}
function cookie($name,$value='',$time=3600,$domain=''){
    $pre=ke\Config::get('cookie.prefix');
    if($value===''){
        if(isset($_COOKIE[$pre.$name])){
            return $_COOKIE[$pre.$name];
        }else{
            return '';
        }
    }elseif(is_null($value)){
        setcookie($pre.$name,null,time()-$time,'/',$domain);
        unset($_COOKIE[$pre.$name]);
    }else{
        setcookie($pre.$name,$value,time()+$time,'/',$domain);
        $_COOKIE[$pre.$name]=$value;
    }
}
function session($name,$value=''){
    if(!isset($_SESSION)){session_start();}
    $pre=\ke\Config::get('session.prefix');
    if($value===''){
        if(isset($_SESSION[$pre.$name])){
            return $_SESSION[$pre.$name];
        }else{
            return '';
        }
    }elseif(is_null($value)){
        if(strpos($name,'.')===false){
            if(isset($pre)){
                unset($_SESSION[ke\Config::get('session_pre')][$name]);
            }else{
                unset($_SESSION[$name]);
            }
        }else{
            list($name1,$name2)=explode('.',$name);
            if(isset($pre)){
                unset($_SESSION[ke\Config::get('session_pre')][$name1][$name2]);
            }else{
                unset($_SESSION[$name1][$name2]);
            }
        }
    }else{
        if(strpos($name,'.')===false){
            if(isset($pre)){
                $_SESSION[ke\Config::get('session_pre')][$name]=$value;
            }else{
                $_SESSION[$name]=$value;
            }
        }else{
            list($name1,$name2)=explode('.',$name);
            if(isset($pre)){
                $_SESSION[ke\Config::get('session_pre')][$name1][$name2]=$value;
            }else{
                $_SESSION[$name1][$name2]=$value;
            }
        }
    }
}
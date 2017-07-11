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
function get_ip($int=false){
    if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
        $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
    else if (isset($_SERVER["HTTP_CLIENT_IP"]))
        $ip = $_SERVER["HTTP_CLIENT_IP"];
    else if (isset($_SERVER["REMOTE_ADDR"]))
        $ip = $_SERVER["REMOTE_ADDR"];
    else if (getenv("HTTP_X_FORWARDED_FOR"))
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    else if (getenv("HTTP_CLIENT_IP"))
        $ip = getenv("HTTP_CLIENT_IP");
    else if (getenv("REMOTE_ADDR"))
        $ip = getenv("REMOTE_ADDR");
    else
        $ip = "0";
    $ip=$int ? ip2long($ip) : $ip;
    return $ip;
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
<?php


namespace ke;


class Request
{
    private static $var=[];

    /**
     * @param $key
     * @param $value
     */
    public static function set($key,$value)
    {
        self::$var[$key]=$value;
    }

    /**
     * @param $key
     * @return mixed
     */
    public static function get($key)
    {
        if(strpos($key,'.')===false){
            return isset(self::$var[$key]) ? self::$var[$key] : null;
        }else{
            list($l,$r)=explode('.',$key);
            return isset(self::$var[$l][$r]) ? self::$var[$l][$r] : null;
        }
    }

    /**
     * @return array
     */
    public static function load()
    {
        return self::$var;
    }

    /**
     * 判断是否为https协议
     * @return bool
     */
    public static function is_https()
    {
        if ( !empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
            return true;
        } elseif ( isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ) {
            return true;
        } elseif ( !empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') {
            return true;
        }
        return false;
    }

    /**
     * 判断当前请求是否ajax
     * @return bool
     */
    public static function is_ajax()
    {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    }

    /**
     * 判断当前请求是否get
     * @return bool
     */
    public static function is_get()
    {
        return $_SERVER['REQUEST_METHOD'] == 'GET';
    }

    /**
     * 判断当前请求是否post
     * @return bool
     */
    public static function is_post()
    {
        return $_SERVER['REQUEST_METHOD'] == 'POST';
    }

}
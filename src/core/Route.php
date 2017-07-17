<?php
/**
 * Name: Route.php
 * User: King east
 * Site: http://cms.iydou.cn/
 */

namespace ke;


use ke\route\Lists;
use ke\route\Register;

class Route
{
    private static $ds;
    /**
     * 启动
     */
    public static function boot()
    {
        if(!is_object(self::$ds)){
            self::$ds=new Register();
        }
        self::$ds->match(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/');
    }

    /**
     * @param $option
     * @param $callback
     */
    public static function group($option,$callback)
    {
        self::$ds=null;
        self::$ds=new Register($option);
        call_user_func($callback);
    }

    /**
     * 注册路由
     * @param $name
     * @param $bind
     * @param string $option
     */
    public static function reg($name,$bind,$option='GET')
    {
        if(!is_object(self::$ds)){
            self::$ds=new Register();
        }
        $domain='';
        if(is_array($option)){
            $domain=isset($option['domain']) ? $option['domain'] : '';
            $method=isset($option['method']) ? $option['method'] : 'GET';
        }else{
            $method=$option;
        }
        return self::$ds->add($name,$bind,$method,$domain);
    }

    /**
     * 注册GET路由
     * @param $name
     * @param $bind
     * @return $this
     */
    public static function get($name,$bind)
    {
        return self::reg($name,$bind);
    }

    /**
     * 注册POST路由
     * @param $name
     * @param $bind
     * @return $this
     */
    public static function post($name,$bind)
    {
        return self::reg($name,$bind,'POST');
    }

    /**
     * 注册所有请求路由
     * @param $name
     * @param $bind
     * @return $this
     */
    public static function any($name,$bind)
    {
        return self::reg($name,$bind,'any');
    }

    /**
     * 生成URL
     * @param $uri
     * @param array $param
     * @return mixed|string
     */
    public static function url($uri,$param=[])
    {
        if(!is_object(self::$ds)){
            self::$ds=new Register();
        }
        return self::$ds->url($uri,$param);
    }

}
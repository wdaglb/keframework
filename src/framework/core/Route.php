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

        self::$ds->match(isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/');
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
     * @param $name
     * @param $bind
     * @param string $type
     */
    public static function register($name,$bind,$type='get')
    {
        if(!is_object(self::$ds)){
            self::$ds=new Register();
        }
        self::$ds->add($name,$bind,$type);
    }

    public static function url($uri,$param=[])
    {
        if(!is_object(self::$ds)){
            self::$ds=new Register();
        }
        return self::$ds->url($uri,$param);
    }

}
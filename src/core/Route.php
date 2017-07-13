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
     * @param $name
     * @param $classname
     * @param $action
     * @param string $type
     */
    public static function register($name,$classname,$action='index',$type='get')
    {
        if(!is_object(self::$ds)){
            self::$ds=new Register();
        }
        return self::$ds->add($name,$classname,$action,$type);
    }

    public static function url($uri,$param=[])
    {
        if(!is_object(self::$ds)){
            self::$ds=new Register();
        }
        return self::$ds->url($uri,$param);
    }

}
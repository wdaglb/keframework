<?php
/**
 * Name: Lists.php
 * User: King east
 * Site: http://cms.iydou.cn/
 */

namespace ke\route;


class Lists
{
    private static $list=[];

    public static function set($l)
    {
        return array_push(self::$list,$l)-1;
    }
    public static function push($index,$data)
    {
        self::$list[$index]=array_merge(self::$list[$index],$data);
    }
    public static function get()
    {
        return self::$list;
    }

}
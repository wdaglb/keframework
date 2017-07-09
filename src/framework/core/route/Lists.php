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
        self::$list[]=$l;
    }
    public static function get()
    {
        return self::$list;
    }

}
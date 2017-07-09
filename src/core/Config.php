<?php

/**
 * Name: Config.php
 * User: King east
 * Site: http://cms.iydou.cn/
 */
namespace ke;
class Config
{
    private static $config=[];
    public static function load($name)
    {
        $file=FRAMEWORK_ROOT.'config/'.$name.'.php';
        if(is_file($file)){
            $array=require($file);
            self::$config[$name]=$array;
        }

    }
    /**
     * 设置配置值
     * @param $name 键名
     * @param $value 值
     * @return void
     */
    public static function set($name,$value=null)
    {
        if(is_array($name)){
            self::$config=array_merge(self::$config,$name);
            return;
        }
        if(strstr($name,'.')===false){
            self::$config[$name]=$value;
        }else{
            list($l,$r)=explode('.',$name);
            self::$config[$l][$r]=$value;
        }
    }

    /**
     * 获取配置值
     * @param $name
     * @return mixed
     */
    public static function get($name)
    {
        if(strstr($name,'.')===false){
            return isset(self::$config[$name]) ? self::$config[$name] : null;
        }else{
            list($l,$r)=explode('.',$name);
            return isset(self::$config[$l][$r]) ? self::$config[$l][$r] : null;
        }
    }

}
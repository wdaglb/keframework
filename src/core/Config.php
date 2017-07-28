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
     * @param $name   节点名
     * @param $values 默认值
     * @return mixed
     */
    public static function get($name,$values=null)
    {
        if(strstr($name,'.')===false){
            return isset(self::$config[$name]) ? self::$config[$name] : $values;
        }else{
            $list=explode('.',$name);
            $config=self::$config;
            // 循环读取
            foreach ($list as $key) {
                // 判断是否存在,不存在直接返回默认值
                if(isset($config[$key])){
                    $config=$config[$key];
                }else{
                    return $values;
                }
            }
            return $config;
        }
    }

}
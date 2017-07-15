<?php
/**
 * Name: Connect.php
 * User: King east
 * Site: http://cms.iydou.cn/
 */

namespace ke\DB;


use ke\Config;
use ke\Exception;
use ke\View;

class Connect
{
    private static $config=[];
    private static $db;
    public static function boot()
    {
        if(is_object(self::$db)) return self::$db;
        try {
            self::$config=Config::get('database');
            if(empty(self::$config)) throw new Exception('数据库没有配置');
            $conn=sprintf('mysql:host=%s;dbname=%s;port=%s',self::$config['host'],self::$config['name'],self::$config['port']);
            self::$db = new \PDO($conn,self::$config['user'],self::$config['pass']);
            self::$db->exec('SET NAMES \''.self::$config['charset'].'\';');
            self::$db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            return self::$db;
        } catch (\PDOException $e) {
            throw new Exception($e->getMessage());
        }

    }


    public static function comSql(&$sql)
    {
        $sql=preg_replace_callback('/`:(\w+)`/',function ($to){
            return '`'.self::$config['prefix'].$to[1].'`';
        },$sql);
    }

}
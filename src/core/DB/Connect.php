<?php


namespace ke\DB;


use ke\View;

class Connect
{
    private static $db;
    public static function boot($config)
    {
        if(is_object(self::$db)) return self::$db;
        try {
            $conn=sprintf('mysql:host=%s;dbname=%s;port=%s',$config['host'],$config['name'],$config['port']);
            self::$db = new \PDO($conn,$config['user'],$config['pass']);
            self::$db->exec('SET NAMES \''.$config['charset'].'\';');
            self::$db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            return self::$db;
        } catch (\PDOException $e) {
            $error=[
                'type'=>$e->getCode(),
                'message'=>$e->getMessage(),
                'file'=>$e->getFile(),
                'line'=>$e->getLine(),
            ];
            View::throwError($error);
        }

    }

}
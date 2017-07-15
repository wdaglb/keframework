<?php
/**
 * Name: DB.php
 * User: King east
 * Site: http://cms.iydou.cn/
 */

namespace ke;


use ke\DB\Connect;
use ke\DB\Query;

class DB
{
    private static $config=[];

    private static $db;

    /**
     * 执行查询语句
     * @param $sql
     * @param array $bind
     * @return mixed
     */
    public static function query($sql,$bind=[])
    {
        try{
            $bind=is_string($bind) ? [$bind] : $bind;
            self::$db=Connect::boot();
            Connect::comSql($sql);
            $sth = self::$db->prepare($sql);
            $sth->execute($bind);
            return $sth;
        } catch (\PDOException $e) {
            throw new Exception($e->getMessage());
            //View::throwError($error);
        }
    }

    /**
     * 执行操作语句
     * @param $sql
     * @param array $bind
     * @return mixed
     */
    public static function execute($sql,$bind=[])
    {
        try{
            $bind=is_string($bind) ? [$bind] : $bind;
            self::$db=Connect::boot();
            Connect::comSql($sql);
            $sth = self::$db->prepare($sql);
            return $sth->execute($bind);
        } catch (\PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }
}
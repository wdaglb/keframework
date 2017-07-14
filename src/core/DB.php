<?php
/**
 * Name: DB.php
 * User: King east
 * Site: http://cms.iydou.cn/
 */

namespace ke;


use ke\DB\Connect;

class DB
{
    private static $config=[];

    private static $db;
    private static function __init()
    {
        $c=Config::get('database');
        if(empty($c)) throw new Exception('数据库没有配置');
        self::$config=array_merge(self::$config,$c);
        self::$db=Connect::boot(self::$config);
    }

    private static function comSql(&$sql)
    {
        self::__init();
        $sql=preg_replace_callback('/`:(\w+)`/',function ($to){
            return '`'.self::$config['prefix'].$to[1].'`';
        },$sql);
    }

    /**
     * 事务处理 0开始事务,1提交事务,2回滚事务
     * @param int $type
     */
    public static function transaction($type=0)
    {
        if($type==0){
            self::$db->transaction();
        }elseif($type==1){
            self::$db->commit();
        }else{
            self::$db->rollBack();
        }
    }
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
            self::comSql($sql);
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
            self::comSql($sql);
            $sth = self::$db->prepare($sql);
            return $sth->execute($bind);
        } catch (\PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

}
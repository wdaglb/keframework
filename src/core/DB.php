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

    /**
     * 获取最新插入的一条ID
     * @return int
     */
    public static function getLastInsertId()
    {
        return self::$db->lastInsertId();
    }

    /**
     * 插入数据
     * @param $table 表名
     * @param array $data 数据
     * @return bool
     * @throws Exception
     */
    public static function insert($table,array $data)
    {
        $start=microtime(true);
        $c=end($data);
        $column='';
        $values='';
        foreach ($data as $key=>$item){
            $bind[$key]=$item;
            if($c==$item){
                $column.="`{$key}`";
                $values.=":{$key}";
            }else{
                $column.="`{$key}`,";
                $values.=":{$key},";
            }
        }

        $sql=sprintf('INSERT INTO `%s` (%s) VALUES (%s)',Connect::getPrefix().$table,$column,$values);
        try{
            self::$db=Connect::boot();
            $sth = self::$db->prepare($sql);
            return $sth->execute($data);
        } catch (\PDOException $e) {
            Log::write(' [ DB ] '.$e->getMessage());
            //throw new Exception($e->getMessage());
            return false;
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
            $bind=is_array($bind) ? $bind : [$bind];
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
            $bind=is_array($bind) ? $bind : [$bind];
            self::$db=Connect::boot();
            Connect::comSql($sql);
            $sth = self::$db->prepare($sql);
            return $sth->execute($bind);
        } catch (\PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }
}
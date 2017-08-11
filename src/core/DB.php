<?php
/**
 * Name: DB.php
 * User: King east
 * Site: http://cms.iydou.cn/
 */

namespace ke;


use ke\DB\Connect;
use ke\DB\Where;
use ke\Exception;

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
            $sth->setFetchMode(\PDO::FETCH_ASSOC);
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
     * @return rowCount       返回受影响行
     */
    public static function execute($sql,$bind=[])
    {
        try{
            $bind=is_array($bind) ? $bind : [$bind];
            self::$db=Connect::boot();
            Connect::comSql($sql);
            $sth = self::$db->prepare($sql);
            $sth->execute($bind);
            return $sth->rowCount();
        } catch (\PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * 执行插入操作
     * @param  string $table  数据表
     * @param  array $data    插入数据
     * @return rowCount       返回受影响行
     */
    public static function create($table,$data)
    {
        $column='';
        $values='';
        $ends=count($data)-1;
        $n=0;
        foreach ($data as $key=>$value) {
            $column.="`{$key}`";
            $values.=":{$key}";
            if($n!=$ends){
                $column.=',';
                $values.=',';
            }
            $n++;
        }
        $sql=sprintf('INSERT INTO `:%s` (%s) VALUES (%s)',$table,$column,$values);
        return self::execute($sql,$data);
    }

    /**
     * 执行更新操作
     * @param  string $table  数据表
     * @param  array  $where  条件
     * @param  array $data    新数据
     * @return rowCount       返回受影响行
     */
    public static function update($table,$where,$data)
    {
        if(empty($data)) throw new Exception("DB update data is null");
        
        $column='';
        $bind=[];
        $ends=count($data)-1;
        $n=0;
        foreach ($data as $key=>$value) {
            $column.="`{$key}`=:{$key}";
            $bind[$key]=$value;
            if($n!=$ends){
                $column.=',';
            }
            $n++;
        }
        $w=new Where($where);
        $sql=sprintf('UPDATE `:%s` SET %s WHERE %s',$table,$column,$w->parseWhere());
        $bind=array_merge($bind,$w->parseBind());
        return self::execute($sql,$bind);
    }

    /**
     * 执行软删除操作
     * @param  string $table  数据表
     * @param  array  $where  条件
     * @return rowCount       返回受影响行
     */
    public static function delete($table,$where)
    {
        $bind=[];
        $w=new Where($where);
        $sql=sprintf('UPDATE `:%s` SET `delete`=1 WHERE %s',$table,$w->parseWhere());
        $bind=array_merge($bind,$w->parseBind());
        return self::execute($sql,$bind);
    }

    /**
     * 执行硬删除操作
     * @param  string $table  数据表
     * @param  array  $where  条件
     * @return rowCount       返回受影响行
     */
    public static function destroy($table,$where)
    {
        $bind=[];
        $w=new Where($where);
        $sql=sprintf('DELETE FROM `:%s` WHERE %s',$table,$w->parseWhere());
        $bind=array_merge($bind,$w->parseBind());
        return self::execute($sql,$bind);
    }
}
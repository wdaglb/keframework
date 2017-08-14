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
     * 执行硬删除操作
     * @param  string $table  数据表
     * @param  array  $where  条件
     * @return rowCount       返回受影响行
     */
    public static function delete($table,$where)
    {
        $bind=[];
        $w=new Where($where);
        $sql=sprintf('DELETE FROM `:%s` WHERE %s',$table,$w->parseWhere());
        $bind=array_merge($bind,$w->parseBind());
        return self::execute($sql,$bind);
    }

    /**
     * 解析
     * @param  string $table 表名
     * @param  array $where 条件
     * @param  string $option 附加信息
     * @return data        数据集合
     */
    private static function parseSql($table,$where,$option=[])
    {
        $field='';
        if(isset($option['field']) && is_array($option['field'])){
            $ends=count($option['field'])-1;
            foreach ($field as $key => $value) {
                $field.='`'.$value.'`';
                if($key!=$ends){
                    $field.=',';
                }
            }
        }else{
            $field='*';
        }
        $limit='';
        if(isset($option['limit']) && is_array($option['limit'])){
            $limit=' LIMIT '.$option['limit'][0].','.$option['limit'][1];
        }
        $order='';
        if(isset($option['order']) && is_array($option['order'])){
            $c=count($option['order'])-1;
            $i=0;
            $order='ORDER BY ';
            foreach ($option['order'] as $key=>$value) {
                $i++;
                $order.="`{$key}` {$value}";
                if($c==$i){
                    $order.=',';
                }
            }

        }
        $w=new Where($where);

        $sql=sprintf('SELECT '.$field.' FROM `:%s` WHERE %s %s %s',$table,$w->parseWhere(),$limit,$order);
        return self::query($sql,$w->parseBind());
    }

    /**
     * 获取一行数据
     * @param  string $table  数据表
     * @param  array  $where  条件
     * @return rowCount       返回数据行
     */
    public static function first($table,$where,$option=[])
    {
        $sql=self::parseSql($table,$where,$option);
        return $sql->fetch();
    }

    /**
     * 获取多行数据
     * @param  string $table  数据表
     * @param  array  $where  条件
     * @return rowCount       返回数据集
     */
    public static function all($table,$where,$option=[])
    {
        $sql=self::parseSql($table,$where,$option);
        return $sql->fetchAll();
    }
}
<?php

namespace ke;

/**
* 模型
*/
class Model
{
	//private $table='';
	
	public function __construct()
	{
		//$class=static::class;
		//$this->table=pascal_to_line($class);

	}

    /**
     * 获取最后插入的ID
     * @return int
     */
	protected function getLastInsertId()
    {
        return DB::getLastInsertId();
    }
    /**
     * 查询
     * @param  string $sql        SQL
     * @param  string/array $bind 绑定数据
     * @return pdo                PDO数据集
     */
	protected function query($sql,$bind=[])
	{
		return DB::query($sql,$bind);
	}
    /**
     * 运行
     * @param  string $sql        SQL
     * @param  string/array $bind 绑定数据
     * @return pdo                成功与否
     */
	protected function execute($sql,$bind=[])
	{
		return DB::execute($sql,$bind);
	}
}
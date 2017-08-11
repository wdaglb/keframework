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

    public function __call($name,$param)
    {
        return call_user_func_array("\\ke\\DB::{$name}",$param);

    }
}
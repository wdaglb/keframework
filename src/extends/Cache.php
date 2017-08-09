<?php 
namespace ke;

use ke\Config;

class Cache
{
	// 驱动对象
	private $class;
	// 默认配置
	private $config=[
		'type'     =>'file',
		'timerout' =>3600,
	];

	public function __construct()
	{
		$config=Config::get('cache','');
		if($config!=''){
			$this->config=array_merge($this->config,$config);
		}
		if(empty($this->config['type'])){
			throw new Exception('[cache:type] '.$this->config['type']);
		}
		if(empty($this->config['timerout'])){
			throw new Exception('[cache:timerout] '.$this->config['timerout']);
		}

		$namespace='ke\\cache\\'.ucwords($this->config['type']);
		$this->class=new $namespace($this->config);
	}

	public function get($key,$value='')
	{
		if(!strpos('.',$key)===false){
			$key=explode('.',$key);
		}
		return $this->class->get($key,$value);
	}

	public function set($key,$value='')
	{
		return $this->class->set($key,$value);
	}
}
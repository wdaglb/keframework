<?php 
namespace ke\cache;

use ke\Exception;

class Redis implements \interfaces\Cache
{
	private $config=[];
	// redis对象
	private $class;
	public function __construct(array $config)
	{
		$this->config=$config;
		$this->class=new \Redis();
		if(!isset($this->config['host'])) throw new Exception('请设定Redis地址');
		if(!isset($this->config['port'])) throw new Exception('请设定Redis端口');

		$this->class->connect($this->config['host'],$this->config['port']);
	}

	public function set($key,$value='')
	{
		$this->class->set($key,serialize($value),$this->config['timerout']);
	}

	public function get($key,$value='')
	{
		$data=$this->class->get($key);
		if($data===false){
			return $value;
		}
		return unserialize($data);
	}

}
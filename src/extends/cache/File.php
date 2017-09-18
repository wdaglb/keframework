<?php 
namespace ke\cache;

class File implements \interfaces\Cache
{
	private $config=[];
	private $dir='';
	public function __construct(array $config)
	{
		$this->config=$config;
		$this->dir=RUNTIME_PATH.'cache/';
		if(!is_dir($this->dir)) mkdir($this->dir,0755);
	}

	public function set($key,$value='',$timeout=null)
	{
		if(is_null($timeout)){
			$timeout=$this->config['timerout'];
		}
		$value = serialize($value);
		$file=$key.'.php';
		file_put_contents($this->dir.$file,serialize(['expire_time'=>$_SERVER['REQUEST_TIME']+$timeout,'data'=>$value]));
	}

	public function get($key,$value='')
	{
		$file=$key.'.php';
		if(is_file($this->dir.$file)){
			$content=file_get_contents($this->dir.$file);
			$data=unserialize($content);
			// 过期删除
			if($_SERVER['REQUEST_TIME']>$data['expire_time']){
				unlink($this->dir.$file);

				return $value;
			}
			return unserialize($data['data']);
		}else{
			return $value;
		}
	}

}
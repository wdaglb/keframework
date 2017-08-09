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

	public function set($key,$value='')
	{
		$file=$key.'.php';
		file_put_contents($this->dir.$file,serialize(['create_time'=>$_SERVER['REQUEST_TIME'],'data'=>$value]));
	}

	public function get($key,$value='')
	{
		$file=$key.'.php';
		if(is_file($file)){
			$content=file_get_contents($file);
			$data=unserialize($content);
			// 过期删除
			if($_SERVER['REQUEST_TIME']-$data['create_time']>$this->config['timerout']){
				unlink($file);
				return $value;
			}
			return $data['data'];
		}else{
			return $value;
		}
	}

}
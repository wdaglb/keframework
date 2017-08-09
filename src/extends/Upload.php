<?php
namespace ke;

class Upload
{
	private $savepath='';

	private $rule=[];

	private $file=[];

	private $error='';

	private $return=[];

	private $mime=[
		'xls'=>['application/x-xls','application/vnd.ms-excel'],
		'xml'=>['text/xml'],
		'apk'=>['application/vnd.android.package-archive'],
		'ipa'=>['application/vnd.iphone'],
		'jpg'=>['image/jpeg','application/x-jpg'],
		'gif'=>['image/gif'],
		'png'=>['image/png','application/x-png'],
		'ppt'=>['application/x-ppt','application/vnd.ms-powerpoint'],
		'txt'=>['text/plain'],
		'css'=>['text/css'],
		'ico'=>['image/x-icon','application/x-ico'],
		'img'=>['application/x-img'],
		'java'=>['java/*'],
		'jpeg'=>['image/jpeg'],
		'm3u'=>['audio/mpegurl'],
		'm4e'=>['video/mpeg4'],
		'mdb'=>['application/x-mdb'],
		'mp2v'=>['video/mpeg'],
		'mp3'=>['audio/mp3'],
		'mp4'=>['video/mpeg4'],
		'mpa'=>['video/x-mpg'],
		'mpd'=>['application/vnd.ms-project'],
		'mpe'=>['video/x-mpeg'],
		'mpeg'=>['video/mpg'],
		'mpv'=>['video/mpg'],
		'rpm'=>['audio/x-pn-realaudio-plugin'],
		'rmvb'=>['application/vnd.rn-realmedia-vbr'],
		'rm'=>['application/vnd.rn-realmedia'],
		'tif'=>['image/tiff'],
		'avi'=>['video/avi'],
		'awf'=>['application/vnd.adobe.workflow'],
		'bmp'=>['application/x-bmp'],
		'doc'=>['application/msword'],
		'dll'=>['application/x-msdownload'],
		'exe'=>['application/x-msdownload'],
	];

	private $error_msg=[
		'1'=>'文件大小超过服务器限制[php]',
		'2'=>'文件大小超过服务器限制[html]',
		'3'=>'文件只有部分被上传',
		'4'=>'没有文件被上传',
		'6'=>'找不到临时文件夹',
		'7'=>'文件写入失败'
	];
	public function __construct($savepath)
	{
		$this->savepath=$savepath;
	}

	/**
	 * 添加文件mime
	 */
	public function addMime($ext,array $rule=[])
	{
		$this->mime[$ext]=$rule;
	}

	/**
	 * 添加规则
	 * @param string $type    类型
	 * @param array  $content 规则内容
	 */
	public function addRule($type,$content)
	{
		$this->rule[$type]=$content;
	}

	public function isPass($file)
	{
		if(empty($_FILES[$file])){
			$this->error="上传对象[{$file}]为空";
			return false;
		}
		$this->file=$_FILES[$file];
		if($this->file['error']){
			$this->error=$this->error_msg[$this->file['error']];
			return false;
		}
		if(!is_uploaded_file($this->file['tmp_name'])){
			$this->error='无文件上传';
			return false;
		}
		$ext=$this->fileExt($this->file['name']);
		// 检测文件类型
		if(isset($this->rule['type'])){
			if(!in_array($ext,$this->rule['type'])){
				$this->error='此类型不允许上传';
				return false;
			}
			if(!in_array($this->file['type'],$this->mime[$ext])){
				$this->error='非法文件类型';
				return false;
			}
		}
		// 检测文件大小
		if(isset($this->rule['max'])){
			if($this->file['size']>$this->rule['max']){
				$this->error='文件大小超过限制';
				return false;
			}
		}
		if(!is_dir($this->savepath)){
			mkdir($this->savepath,0755,true);
		}
		$name=strtoupper(md5(uniqid(mt_rand(0,99999))));

		$this->return=['path'=>$this->savepath,'name'=>$name,'ext'=>$ext];

		move_uploaded_file($this->file['tmp_name'],$this->savepath.$name.'.'.$ext);
		return true;
	}

	/**
	 * 取回保存文件
	 * @return
	 */
	public function getFile()
	{
		return $this->return;
	}
	/**
	 * 取回错误信息
	 * @return string 错误信息
	 */
	public function getError()
	{
		return $this->error;
	}

	private function fileExt($file)
	{
		return pathinfo($file, PATHINFO_EXTENSION);
	}


}
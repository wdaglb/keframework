<?php
/**
 * Name: Twig.php
 * User: King east
 * Site: http://cms.iydou.cn/
 */

namespace ke\view;


use app\addons\Filters;
use app\addons\Functions;
use ke\Exception;
use ke\Request;
use ke\Config;

class Twig implements \interfaces\Template
{
    private $var=[];

    private $config=[];

    private $live;

    private $path='';
    private function _init()
    {
        $this->path=APP_PATH.(isset($this->config['module']) ? $this->config['module'].'/' : '').'view/';
        if(is_object($this->live)) return $this->live;
        try{
            $loader = new \Twig_Loader_Filesystem($this->path);
            $this->live = new \Twig_Environment($loader, array(
                'cache' => RUNTIME_PATH.$this->config['compile'],
                'debug'=>DEBUG
            ));
            $this->var['__WEB_PATH__']=__WEB_PATH__;
            $this->live->addFunction(new \Twig_SimpleFunction('url',function($uri,$params=[]){
                return url($uri,$params);
            }));
            $this->live->addFunction(new \Twig_SimpleFunction('assets',function($name){
                $src=__WEB_PATH__.$name;
                if(DEBUG){
                    $src.='?v='.$_SERVER['REQUEST_TIME'];
                }
                return $src;
            }));
            if(class_exists('app\addons\Functions')){
                $class=new Functions();
                $l=get_class_methods($class);
                foreach ($l as $m) $this->live->addFunction(new \Twig_SimpleFunction($m,[$class,$m]));
            }
            if(class_exists('app\addons\Filters')){
                $class=new Filters();
                $l=get_class_methods($class);
                foreach ($l as $m) $this->live->addFilter(new \Twig_SimpleFilter($m,[$class,$m]));
            }
        }catch (\Twig_Error $e){
            throw new Exception($e->getMessage());
        }
    }
    public function __construct(array $name)
    {
        $this->setConfig($name);
    }
    public function setConfig(array $name)
    {
        $this->config=array_merge($this->config,$name);
    }
    public function assign($name,$value=null)
    {
        if(is_array($name)){
            $this->var=array_merge($this->var,$name);
        }else{
            $this->var[$name]=$value;
        }
    }

    /**
     * 判断模板文件是否存在
     * @param $name
     * @return bool
     */
    public function isTemplateFile($name)
    {
        $this->_init();
        return is_file($this->path.$this->getFilePath($name));
    }

    private function getFilePath($name)
    {
        if($name==''){
            $pt=$this->config['controller'].'/'.$this->config['action'];
        }else{
            $pt=$name;
        }
        return $pt.$this->config['suffix'];
    }

    /**
     * 页面渲染
     * @param $name
     * @return string
     */
    public function render($name='')
    {
        $this->_init();
        try{
            return $this->live->render($this->getFilePath($name),$this->var);
        }catch (\Twig_Error_Loader $e){
            throw new Exception($e->getMessage());
        }
    }

}
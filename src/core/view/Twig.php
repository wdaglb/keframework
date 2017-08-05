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
    private function _init()
    {
        try{
            $loader = new \Twig_Loader_Filesystem(APP_PATH.(isset($this->config['module']) ? $this->config['module'].'/' : '').'view/');
            $this->live = new \Twig_Environment($loader, array(
                'cache' => RUNTIME_PATH.$this->config['compile'],
                'debug'=>DEBUG
            ));
            $this->live->addFunction(new \Twig_SimpleFunction('url',function($uri,$params=[]){
                return url($uri,$params);
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
        return is_file(APP_PATH.(isset($this->config['module']) ? $this->config['module'].'/' : '').'view/'.$name.$this->config['suffix']);
    }

    /**
     * 页面渲染
     * @param $name
     * @return string
     */
    public function render($name='')
    {
        $this->_init();
        if($name=='') $name=Config::get('is_tpl_action') ? Request::get('action') : $name;
        try{
            return $this->live->render((isset($this->config['controller']) ? $this->config['controller'].'/' : '').$name.$this->config['suffix'],$this->var);
        }catch (\Twig_Error_Loader $e){
            throw new Exception($e->getMessage());
        }
    }

}
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

class Twig implements \ke\interfaces\Template
{
    private $var=[];

    private $config=[];

    private $live;
    private function _init()
    {
        try{
            $loader = new \Twig_Loader_Filesystem(Request::get('system.root').$this->config['path'].(isset($this->config['module']) ? $this->config['module'] : ''));
            $this->live = new \Twig_Environment($loader, array(
                'cache' => Request::get('system.root').$this->config['compile'],
                'debug'=>Request::get('debug')
            ));
            $class=new Functions();
            $l=get_class_methods($class);
            foreach ($l as $m) $this->live->addFunction(new \Twig_SimpleFunction($m,[$class,$m]));
            $class=new Filters();
            $l=get_class_methods($class);
            foreach ($l as $m) $this->live->addFilter(new \Twig_SimpleFilter($m,[$class,$m]));
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
        return is_file(Request::get('system.root').$this->config['path'].(isset($this->config['module']) ? $this->config['module'] : '').'/'.$name.$this->config['suffix']);
    }

    /**
     * 页面渲染
     * @param $name
     * @return string
     */
    public function render($name)
    {
        $this->_init();
        try{
            return $this->live->render((isset($this->config['controller']) ? $this->config['controller'].'/' : '').$name.$this->config['suffix'],$this->var);
        }catch (\Twig_Error_Loader $e){
            throw new Exception($e->getMessage());
        }
    }

}
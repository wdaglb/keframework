<?php
/**
 * Name: Template.php
 * User: King east
 * Site: http://cms.iydou.cn/
 */

namespace ke;


use app\addons\Filters;
use app\addons\Functions;

class Template
{
    private $config=[
        // 模板路径
        'template_path'=>'./resources/views/',
        // 编译路径
        'compile_path'=>'./runtime/compile/',
        // 模板后缀
        'suffix'=>'.htm',
    ];
    private $var=[];

    private $live;
    private function _init()
    {
        if(is_object($this->live)) return;
        try{
            $loader = new \Twig_Loader_Filesystem(Request::get('system.root').$this->config['template_path'].(isset($this->config['module']) ? $this->config['module'] : ''));
            $this->live = new \Twig_Environment($loader, array(
                'cache' => Request::get('system.root').$this->config['compile_path'],
                'debug'=>Request::get('debug')
            ));
            $class=new Functions();
            $l=get_class_methods($class);
            foreach ($l as $m) $this->live->addFunction(new \Twig_SimpleFunction($m,[$class,$m]));
            $class=new Filters();
            $l=get_class_methods($class);
            foreach ($l as $m) $this->live->addFilter(new \Twig_SimpleFilter($m,[$class,$m]));
        }catch (\Twig_Error $e){
            View::throwError([
                'message'=>$e->getMessage(),
                'file'=>$e->getFile(),
                'line'=>$e->getLine()
            ]);
        }
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

    private function getUpUrl()
    {
        return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
    }
    /**
     * 成功提示
     * @param $message
     * @param null $url
     * @return string
     */
    public function success($message,$url=null,$wait=3){
        try{
            if(Request::is_ajax()){
                header('Content-type: application/json');
                echo json_encode(['status'=>true,'message'=>$message]);
                exit;
            }
            $url=is_null($url) ? $this->getUpUrl() : $url;
            if(is_file(Request::get('system.root').$this->config['template_path'].(isset($this->config['module']) ? $this->config['module'] : '').'/success'.$this->config['suffix'])){
                $this->_init();
                echo $this->live->render('success'.$this->config['suffix'],['status'=>true,'message'=>$message,'url'=>$url,'wait'=>$wait]);
                exit;
            }else{
                $status=true;
                require Request::get('system.framework').'tpl/jump.php';
                exit;
            }
        }catch (\Twig_Error $e){
            $error=[
                'type'=>$e->getCode(),
                'message'=>$e->getMessage(),
                'file'=>$e->getFile(),
                'line'=>$e->getLine()
            ];
            View::throwError($error);
        }
    }
    /**
     * 错误提示
     * @param $message
     * @param null $url
     * @return string
     */
    public function error($message,$url=null,$wait=3){
        try{
            if(Request::is_ajax()){
                header('Content-type: application/json');
                echo json_encode(['status'=>false,'message'=>$message]);
                exit;
            }
            $url=is_null($url) ? $this->getUpUrl() : $url;
            if(is_file(Request::get('system.root').$this->config['template_path'].(isset($this->config['module']) ? $this->config['module'] : '').'/error'.$this->config['suffix'])){
                $this->_init();
                echo $this->live->render('error'.$this->config['suffix'],['status'=>true,'message'=>$message,'url'=>$url,'wait'=>$wait]);
                exit;
            }else{
                $status=false;
                require Request::get('system.framework').'tpl/jump.php';
                exit;
            }
        }catch (\Twig_Error $e){
            $error=[
                'type'=>$e->getCode(),
                'message'=>$e->getMessage(),
                'file'=>$e->getFile(),
                'line'=>$e->getLine()
            ];
            View::throwError($error);
        }
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
            return $this->live->render($name.$this->config['suffix'],$this->var);
        }catch (\Twig_Error_Loader $e){
            $error=[
                'type'=>$e->getCode(),
                'message'=>$e->getMessage(),
                'file'=>$e->getFile(),
                'line'=>$e->getLine(),
            ];
            View::throwError($error);
        }
    }

}
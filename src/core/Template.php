<?php
/**
 * Name: Template.php
 * User: King east
 * Site: http://cms.iydou.cn/
 */

namespace ke;


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

    /**
     * 设置模板参数
     * @param array $config
     */
    public function setConfig($config=[])
    {
        $this->config=array_merge($this->config,$config);
    }

    /**
     * 初始化模板
     */
    private function _init()
    {
        if(is_object($this->live)) return;
        try{
            $tp=$this->config['template_path'].(isset($this->config['module']) ? $this->config['module'].'/' :'');
            $loader = new \Twig_Loader_Filesystem(Request::get('system.root').$tp);
            $this->live = new \Twig_Environment($loader, array(
                'cache' => $this->config['compile_path'],
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
    public function assign($name,$value=null){
        if(is_array($name)){
            $this->var=array_merge($this->var,$name);
        }else{
            $this->var[$name]=$value;
        }
    }

    /**
     * 成功提示
     * @param $message
     * @param null $url
     * @return string
     */
    public function success($message,$url=null){
        if(Request::is_ajax()){
            return ['status'=>true,'message'=>$message];
        }
        try{
            $this->_init();
            return $this->live->render('success'.$this->config['suffix'],['message'=>$message,'url'=>$url]);
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
    public function error($message,$url=null){
        if(Request::is_ajax()){
            return ['status'=>false,'message'=>$message];
        }
        try{
            $this->_init();
            return $this->live->render('error'.$this->config['suffix'],['message'=>$message,'url'=>$url]);
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
        try{
            $this->_init();
            return $this->live->render($name.$this->config['suffix'],$this->var);
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

}
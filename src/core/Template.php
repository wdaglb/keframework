<?php
/**
 * Name: Template.php
 * User: King east
 * Site: http://cms.iydou.cn/
 */

namespace ke;


use app\addons\Filters;
use app\addons\Functions;
use ke\exception\ErrorException;

class Template
{
    private $instance;

    public function __construct()
    {
        $c=Config::get('template');
        if(!isset($c['type'])) throw new Exception('请设置template引擎类型[type]');
        if(!isset($c['path'])) throw new Exception('请设置template目录[path]');
        if(!isset($c['compile'])) throw new Exception('请设置template编译目录[compile]');
        if(!isset($c['suffix'])) throw new Exception('请设置template后缀名[suffix]');
        $type='ke\\view\\'.ucwords($c['type']);
        $this->instance=new $type($c);
    }

    public function setConfig(array $option)
    {
        $this->instance->setConfig($option);
    }

    /**
     * 传入变量
     * @param $name
     * @param array $value
     */
    public function assign($name,$value=[])
    {
        $this->instance->assign($name,$value);
    }

    /**
     * 成功提示
     * @param $message
     * @param null $url
     * @return string
     */
    public function success($message,$url=null,$wait=3){
        KE::resetToken();
        if(Request::isAjax()){
            header('Content-type: application/json');
            echo json_encode(['status'=>true,'message'=>$message]);
            exit;
        }
        if($this->instance->isTemplateFile('success')){
            $this->instance->assign(['status'=>true,'message'=>$message,'url'=>$url,'wait'=>$wait]);
            echo $this->instance->render('success');
            exit;
        }else{
            $status=true;
            require Request::get('system.framework').'tpl/jump.php';
            exit;
        }
    }
    /**
     * 错误提示
     * @param $message
     * @param null $url
     * @return string
     */
    public function error($message,$url=null,$wait=3){
        if(Request::isAjax()){
            header('Content-type: application/json');
            echo json_encode(['status'=>false,'message'=>$message]);
            exit;
        }
        if($this->instance->isTemplateFile('error')){
            $this->instance->assign(['status'=>false,'message'=>$message,'url'=>$url,'wait'=>$wait]);
            echo $this->instance->render('error');
            exit;
        }else{
            $status=false;
            require Request::get('system.framework').'tpl/jump.php';
            exit;
        }
    }

    /**
     * 页面渲染
     * @param $name
     * @return string
     */
    public function render($name)
    {
        return $this->instance->render($name);
    }

}
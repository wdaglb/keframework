<?php


namespace ke;


use function Sodium\version_string;

class Controller
{
    /**
     * 前置器
     * @var array
     */
    protected $fronts=[];

    final public function __call($name, $arguments)
    {
        if(in_array($name,$this->fronts)) return call_user_func_array([$this,$name],$arguments);
    }

    public function getAttr($name)
    {
        return isset($this->$name) ? $this->$name : null;
    }


    /**
     * 设置模板模块
     * @param $name
     */
    protected function setTemplateModule($name)
    {
        view()->setConfig(['module'=>$name]);
    }

    /**
     * 渲染模板
     * @param $name
     * @return string
     */
    protected function render($name='')
    {
        return view()->render($name);
    }

    /**
     * 传递变量到模板
     * @param $name
     * @param array $value
     */
    protected function assign($name,$value=[])
    {
        return view()->assign($name,$value);
    }

    /**
     * @param $message
     * @param null $url
     * @return string
     */
    protected function success($message,$url=null)
    {
        return view()->success($message,$url);
    }

    /**
     * 跳转
     * @param $url
     * @param array $param
     */
    protected function redirect($url,$param=[])
    {
        if(Request::isAjax()){
            header('Content-type:application/json');
            if(!empty($param) && is_array($param)){
                $value=array_merge(['status'=>false,'message'=>'跳转提示','url'=>$url],$param);
            }else{
                $value=['status'=>false,'message'=>'跳转提示','url'=>$url];
            }
            echo json_encode($value);
            exit;
        }
        if(substr($url,0,3)==='ef:'){
            $url=substr($url,3);
            echo '<script type="text/javascript">top.location.href="'.Route::url($url,$param).'";</script>';
        }else{
            header('Location:'.Route::url($url,$param));
        }
        exit;
    }

    /**
     * @param $message
     * @param null $url
     * @return string
     */
    protected function error($message,$url=null)
    {
        return view()->error($message,$url);
    }

}
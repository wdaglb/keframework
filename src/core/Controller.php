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
     * @param $message
     * @param null $url
     * @return string
     */
    protected function error($message,$url=null)
    {
        return view()->error($message,$url);
    }

    /**
     * 格式化JSON输出
     * @param  int    $code    状态码
     * @param  string $message 消息
     * @param  array $data     返回数据
     * @return void
     */
    protected function json($code,$message='',$data=[])
    {
        View::json($code,$message,$data);
    }

    /**
     * 跳转
     * @param $url
     * @param array $param
     */
    protected function redirect($url,$param=[])
    {
        if(Request::isAjax()){
            return View::json(1,'跳转',['url'=>url($url,$param)]);
        }
        if(substr($url,0,3)==='ef:'){
            $url=substr($url,3);
            echo '<script type="text/javascript">top.location.href="'.url($url,$param).'";</script>';
        }else{
            header('Location:'.url($url,$param));
        }
        exit;
    }

}
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
    protected function render($name)
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

}
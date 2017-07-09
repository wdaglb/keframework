<?php


namespace ke;


class Controller
{
    private $view;
    public function __init($option=[])
    {
        if(is_object($this->view)) return;
        $this->view=new Template($option);
    }

    /**
     * 设置模板模块
     * @param $name
     */
    protected function setTemplateModule($name)
    {
        $this->__init(['module'=>$name]);
    }

    /**
     * 渲染模板
     * @param $name
     * @return string
     */
    protected function render($name)
    {
        $this->__init();
        return $this->view->render($name);
    }

    /**
     * 传递变量到模板
     * @param $name
     * @param array $value
     */
    protected function assign($name,$value=[])
    {
        $this->__init();
        return $this->view->assign($name,$value);
    }

    /**
     * @param $message
     * @param null $url
     * @return string
     */
    protected function success($message,$url=null)
    {
        $this->__init();
        return $this->view->success($message,$url);
    }

    /**
     * @param $message
     * @param null $url
     * @return string
     */
    protected function error($message,$url=null)
    {
        $this->__init();
        return $this->view->error($message,$url);
    }

}
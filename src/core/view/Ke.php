<?php
/**
 * Name: Ke.php
 * User: King east
 * Site: http://cms.iydou.cn/
 */

namespace ke\view;


use ke\interfaces\Template;
use ke\Request;

class Ke implements Template
{
    private $config=[];

    public function __construct(array $name)
    {
        $this->setConfig($name);
    }
    public function setConfig(array $name)
    {
        $this->config=array_merge($this->config,$name);
    }
    public function isTemplateFile($name)
    {
        return is_file($this->getTemplateFile($name));
    }
    public function assign($name, $value = null)
    {
        // TODO: Implement assign() method.
    }

    /**
     * 取模板文件绝对路径
     * @param $name
     * @return string
     */
    private function getTemplateFile($name)
    {
        return Request::get('system.root').$this->config['path'].(isset($this->config['module']) ? $this->config['module'] : '').'/'.$name.$this->config['suffix'];
    }

    private function getTemplateContent($name)
    {

    }

    /**
     * @param $name
     */
    public function render($name)
    {
        $this->getTemplateFile($name);
        // TODO: Implement render() method.
    }

}
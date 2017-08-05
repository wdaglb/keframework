<?php
/**
 * Name: Template.php
 * User: King east
 * Site: http://cms.iydou.cn/
 */

namespace interfaces;


interface Template
{
    /**
     * 初始化
     * Template constructor.
     * @param array $name
     */
    public function __construct(array $name);

    /**
     * 设置参数
     * @param array $name
     * @return mixed
     */
    public function setConfig(array $name);

    /**
     * 传入变量
     * @param $name
     * @param null $value
     * @return mixed
     */
    public function assign($name,$value=null);

    /**
     * 判断模板文件是否存在
     * @param $name
     * @return bool
     */
    public function isTemplateFile($name);

    /**
     * 页面渲染
     * @param $name
     * @return string
     */
    public function render($name);


}
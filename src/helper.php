<?php
/**
 * Name: helper.php
 * User: King east
 * Site: http://cms.iydou.cn/
 */

/**
 * 视图
 * @return \ke\Template|mixed
 */
function view()
{
    $view=\ke\Request::get('view');
    if(is_null($view)){
        $view=new \ke\Template();
        \ke\Request::set('view',$view);
    }
    return $view;
}

/**
 * 生成URL
 * @param $uri
 * @param $param
 * @return mixed|string
 */
function url($uri,$param=[])
{
    return \ke\Route::url($uri,$param);
}
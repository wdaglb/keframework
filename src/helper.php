<?php
/**
 * Name: helper.php
 * User: King east
 * Site: http://cms.iydou.cn/
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
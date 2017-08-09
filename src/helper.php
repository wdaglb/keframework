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
 * 命名空间助手
 * @param  string $name 类名称
 * @return model        返回类对象
 */
function n($name,$dir='\\ke\\')
{
    $new=storage('namespace_'.$name);
    if($new==''){
        $class=$dir.ucwords($name);
        $new=new $class();
        storage('namespace_'.$name,$new);
    }
    return $new;
}

/**
 * 模型助手
 * @param  string $name 模型名称
 * @return model        返回模型对象
 */
function m($name)
{
    $new=storage('model_'.$name);
    if($new==''){
        $class='\\app\\model\\'.ucwords($name);
        $new=new $class();
        storage('model_'.$name,$new);
    }
    return $new;
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

/**
 * 全局变量
 * @param  string $key   节点名
 * @param  string $value 值
 * @return mixed
 */
function storage($key='',$value='')
{
    if($key=='') return $GLOBALS;
    if($value==''){
        return isset($GLOBALS[$key]) ? $GLOBALS[$key] : null;
    }else{
        return $GLOBALS[$key]=$value;
    }
}
/**
 * 获取GET数据
 * @param string $key
 * @return string
 */
function get($key='',$value='')
{
    if($key=='') return $_GET;
    return isset($_GET[$key]) ? $_GET[$key] : $value;
}

/**
 * 获取POST数据
 * @param string $key
 * @return string
 */
function post($key='',$value='')
{
    if($key=='') return $_POST;
    return isset($_POST[$key]) ? $_POST[$key] : $value;
}


/**
 * 设置/获取 COOKIE
 * @param $name
 * @param string $value
 * @param int $time
 * @param string $domain
 * @return string
 */
function cookie($name,$value='',$time=3600,$domain=''){
    $pre=ke\Config::get('cookie.prefix');
    if($value===''){
        if(isset($_COOKIE[$pre.$name])){
            return $_COOKIE[$pre.$name];
        }else{
            return '';
        }
    }elseif(is_null($value)){
        setcookie($pre.$name,null,time()-$time,'/',$domain);
        unset($_COOKIE[$pre.$name]);
    }else{
        setcookie($pre.$name,$value,time()+$time,'/',$domain);
        $_COOKIE[$pre.$name]=$value;
    }
}

/**
 * 设置/获取 SESSION
 * @param $name
 * @param string $value
 * @return string
 */
function session($name,$value=''){
    if(!isset($_SESSION)) session_start();
    $pre=\ke\Config::get('session.prefix');
    if($value===''){
        if($pre==''){
            return isset($_SESSION[$name]) ? $_SESSION[$name] : '';
        }else{
            return isset($_SESSION[$pre][$name]) ? $_SESSION[$pre][$name] : '';
        }
    }elseif(is_null($value)){
        if($pre==''){
            unset($_SESSION[$name]);
        }else{
            unset($_SESSION[$pre][$name]);
        }
    }else{
        if($pre==''){
            $_SESSION[$name]=$value;
        }else{
            $_SESSION[$pre][$name]=$value;
        }
    }
}
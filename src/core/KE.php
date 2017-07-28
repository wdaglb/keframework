<?php
/**
 * Name: KE.php
 * User: King east
 * Site: http://cms.iydou.cn/
 */
namespace ke;

class KE
{
    /**
     * 启动框架
     * @param array $option
     */
    public static function boot()
    {
        include(__DIR__.'/../constant.php');
        new Error();
        spl_autoload_register('ke\KE::autoload');
        require CORE_PATH.'helper.php';
        require CORE_PATH.'functions.php';
        if(is_file(CONF_PATH.'config.php')){
            $config=require CONF_PATH.'config.php';
            Config::set($config);
            unset($config);
        }

        if(is_file(APP_PATH.'common.php')){
            require APP_PATH.'common.php';
        }
        if(is_file(APP_PATH.'route.php')){
            require APP_PATH.'route.php';
        }

        Route::boot();
        //session_destroy();
    }


    public static function autoload($class)
    {
        $pre=explode('\\',$class);
        if($pre[0]=='app'){
            $pre[0]=APP_PATH;
            $newclass=implode('/',$pre);
            $path=str_replace('\\','/',$newclass).'.php';
            if(is_file($path)){
                require $path;
            }else{
                return false;
            }
        }else{
            $path=EXTENDS_PATH.str_replace('\\','/',$class).'.php';
            if(is_file($path)){
                require $path;
            }else{
                return false;
            }
        }
        return false;
    }

}
<?php
/**
 * Name: KE.php
 * User: King east
 * Site: http://cms.iydou.cn/
 */
namespace ke;

define('VERSION','1.0.0');
class KE
{
    /**
     * 启动框架
     * @param array $option
     */
    public static function boot($option=[])
    {
        session_start();
        Request::set('debug',empty($option['debug']) ? false : $option['debug']);
        if(Request::get('debug')){
            Request::set('start_time',microtime(true));
        }
        new Error();
        if(!isset($option['root'])) View::throwError(['message'=>'请定义主路径[root]']);
        $GLOBALS['ROOT']=$option['root'];
        $GLOBALS['FRAMEWORK_ROOT']=__DIR__.'/../';
        Request::set('system',[
            'root'=>$option['root'],
            'framework'=>$GLOBALS['FRAMEWORK_ROOT']
        ]);
        spl_autoload_register('ke\KE::autoload');
        require $GLOBALS['FRAMEWORK_ROOT'].'helper.php';
        require $GLOBALS['FRAMEWORK_ROOT'].'functions.php';
        if(!is_file($GLOBALS['ROOT'].'config/config.php')) View::throwError(['message'=>'请创建配置文件[主目录/config/config.php]']);
        $config=require $GLOBALS['ROOT'].'config/config.php';
        Config::set($config);
        unset($config);

        if(is_file($GLOBALS['ROOT'].'app/common.php')){
            require $GLOBALS['ROOT'].'app/common.php';
        }
        if(!is_file($GLOBALS['ROOT'].'app/route.php')) View::throwError(['message'=>'请创建路由文件[主目录/app/route.php]']);
        require $GLOBALS['ROOT'].'app/route.php';

        Route::boot();
        //session_destroy();
    }


    public static function autoload($class)
    {
        $pre=explode('\\',$class);
        if($pre[0]=='app'){
            $path=$GLOBALS['ROOT'].str_replace('\\','/',$class).'.php';
            if(is_file($path)){
                require $path;
            }else{
                return false;
            }
        }else{
            $path=$GLOBALS['ROOT'].'extend/'.str_replace('\\','/',$class).'.php';
            if(is_file($path)){
                require $path;
            }else{
                return false;
            }
        }
        return false;
    }

}
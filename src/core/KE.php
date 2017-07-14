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
        new Error();
        Request::set('debug',isset($option['debug']) ? $option['debug'] : false);
        if(!isset($option['root'])) View::throwError(['message'=>'请定义主路径[root]']);
        Request::set('system',[
            'root'=>$option['root'],
            'framework'=>__DIR__.'/../'
        ]);
        self::autoload();
        require Request::get('system.framework').'helper.php';
        require Request::get('system.framework').'functions.php';

        if(!is_file(Request::get('system.root').'config/config.php')) View::throwError(['message'=>'请创建配置文件[主目录/config/config.php]']);
        $config=require Request::get('system.root').'config/config.php';
        Config::set($config);
        if(isset($config['csrf']['status']) && $config['csrf']['status'] && $config['csrf']['name']){
            $token=session('__csrf_token__');
            if($token==null){
                $token=sha1(mt_rand(1111,9999));
                session('__csrf_token__',$token);
            }
            view()->assign('csrf_name',$config['csrf']['name']);
            view()->assign('csrf_token',$token);
            if(Request::is_post()){
                if(empty($_POST[$config['csrf']['name']])){
                    View::error('非法操作');
                }
                if($_POST[$config['csrf']['name']]!=$token){
                    View::error('非法操作');
                }
            }

        }
        if(!is_file(Request::get('system.root').'app/route.php')) View::throwError(['message'=>'请创建路由文件[主目录/app/route.php]']);
        require Request::get('system.root').'app/route.php';

        Route::boot();
    }


    public static function autoload()
    {
        spl_autoload_register(function ($class){
            if(substr($class,0,4)=='app\\'){
                $path=Request::get('system.root').str_replace('\\','/',$class).'.php';
                if(is_file($path)){
                    require $path;
                }else{
                    return false;
                }
            }
            return false;
        });
    }

}
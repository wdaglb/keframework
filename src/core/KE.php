<?php
/**
 * Name: KE.php
 * User: King east
 * Site: http://cms.iydou.cn/
 */
namespace ke;
define('FRAMEWORK_ROOT',__DIR__.'/../');
define('VERSION','1.0.00');
// HOOK类型
define('HOOK_ROUTE_START',1);
define('HOOK_ROUTE_END',2);
define('HOOK_VIEW_START',3);
define('HOOK_VIEW_END',4);
class KE
{
    private static $option=[];
    public static function boot($option=[])
    {
        if(!isset($option['root'])) return View::throwError(['message'=>'请定义主路径[root]']);
        self::$option=$option;
        self::autoload();
        require FRAMEWORK_ROOT.'functions.php';

        $config=require $option['root'].'config/config.php';
        Config::set($config);
        $c=$config['csrf'];
        if(isset($c['status']) && $c['status'] && $c['name']){
            $token=session($c['name']);
            if($token==''){
                $token=sha1(mt_rand(1111,9999));
                session($c['name'],$token);
            }
            if(Request::is_post()){
                if(empty($_POST[$c['name']])){
                    View::error('非法操作');
                }
                if($_POST[$c['name']]!=$token){
                    View::error('非法操作');
                }
            }

        }
        require $option['root'].'app/route.php';

        Route::boot();
    }


    public static function autoload()
    {
        spl_autoload_register(function ($class){
            if(substr($class,0,4)=='app\\'){
                $path=self::$option['root'].str_replace('\\','/',$class).'.php';
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
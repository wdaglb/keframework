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
    public static function boot($option=[])
    {
        Request::set('debug',isset($option['debug']) ? $option['debug'] : false);
        if(!isset($option['root'])) View::throwError(['message'=>'请定义主路径[root]']);
        Request::set('system',[
            'root'=>$option['root'],
            'framework'=>__DIR__.'/../'
        ]);
        self::autoload();
        require Request::get('system.framework').'helper.php';
        require Request::get('system.framework').'functions.php';

        $config=require Request::get('system.root').'config/config.php';
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
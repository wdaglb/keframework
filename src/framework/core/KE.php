<?php
/**
 * Name: KE.php
 * User: King east
 * Site: http://cms.iydou.cn/
 */
namespace ke;
class KE
{
    public static function boot()
    {
        new Exception();
        require ROOT.'framework/functions.php';
        $config=require ROOT.'config/config.php';
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
        require ROOT.'app/route.php';

        Route::boot();
        Hook::boot(HOOK_ROUTE_END);
    }

}
<?php
/**
 * Name: View.php
 * User: King east
 * Site: http://cms.iydou.cn/
 */

namespace ke;


class View
{
    public static function error($message)
    {
        if(Request::is_ajax()){
            header('Content-type:application/json');
            echo json_encode(['status'=>false,'message'=>$message]);
            exit;
        }
        require FRAMEWORK_ROOT.'tpl/msg.php';
        die();
    }
    public static function throwError(array $error)
    {
        if(!defined('DEBUG') || !DEBUG){
            $debug=false;
        }else{
            $debug=true;
        }
        if(Request::is_ajax()){
            header('Content-type:application/json');
            if(!$debug){
                $error['message']='系统异常停止';
            }
            echo json_encode(['status'=>false,'message'=>$error['message']]);
            exit;
        }
        require FRAMEWORK_ROOT.'tpl/error.php';
        die();

    }

}
<?php
/**
 * Name: View.php
 * User: King east
 * Site: http://cms.iydou.cn/
 */

namespace ke;


class View
{
    public static function json($code,$message='',$data=[])
    {
        header('Content-type:application/json');
        echo json_encode(['code'=>$code,'message'=>$message,'result'=>$data]);
        exit;
    }
    public static function error($message)
    {
        Log::write(' [ error ] '.$message);
        require CORE_PATH.'tpl/msg.php';
        die();
    }

}
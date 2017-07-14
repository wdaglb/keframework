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
        Log::write(' [ error ] '.$message);
        if(Request::is_ajax()){
            header('Content-type:application/json');
            echo json_encode(['status'=>false,'message'=>$message]);
            exit;
        }
        require Request::get('system.framework').'tpl/msg.php';
        die();
    }
    public static function throwError(array $error)
    {
        if(Request::get('debug')==true){
            $debug=true;
        }else{
            $debug=false;
        }
        $error['message']=isset($error['message']) ? $error['message'] : null;
        $error['file']=isset($error['file']) ? $error['file'] : null;
        $error['line']=isset($error['line']) ? $error['line'] : null;
        Log::write(sprintf(" [ time ] %s [ 加载文件数 ] %s \r\n [ error ] %s\r\n [ file ] %s [ line ] %s",date('Y-m-d H:i:s'),count(get_included_files()),$error['message'],$error['file'],$error['line']));
        if(Request::is_ajax()){
            header('Content-type:application/json');
            if(!$debug){
                $error['message']='系统异常停止';
            }
            echo json_encode(['status'=>false,'message'=>$error['message']]);
            exit;
        }
        require Request::get('system.framework').'tpl/error.php';
        die();

    }

}
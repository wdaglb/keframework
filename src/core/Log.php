<?php
/**
 * Name: Log.php
 * User: King east
 * Site: http://cms.iydou.cn/
 */

namespace ke;


class Log
{
    public static function write($msg)
    {
        $dir=RUNTIME_PATH.'log/'.date('Ymd',$_SERVER['REQUEST_TIME']).'/';
        if(!is_dir($dir)) mkdir($dir,0777,true);
        $filename=date('H').'.log';
        file_put_contents($dir.$filename,"===========================================\r\n\r\n".$msg."\r\n",FILE_APPEND);
    }


}
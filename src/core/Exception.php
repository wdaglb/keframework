<?php
/**
 * Name: Exception.php
 * User: King east
 * Site: http://cms.iydou.cn/
 */

namespace ke;


class Exception
{
    public function __construct()
    {
        error_reporting(E_ALL);
        //错误处理
        set_error_handler([$this,'error_functions']);
        //异常处理
        set_exception_handler([$this,'exception_functions']);
        //致命错误
        register_shutdown_function([$this,'shutdown_functions']);
    }
    public function error_functions($e1,$e2,$e3,$e4)
    {
        $error=[
            'type'=>$e1,
            'message'=>$e2,
            'file'=>$e3,
            'line'=>$e4
        ];
        View::throwError($error);
    }
    public function exception_functions($e)
    {
        $error=[
            'type'=>$e->getCode(),
            'message'=>$e->getMessage(),
            'file'=>$e->getFile(),
            'line'=>$e->getLine()
        ];
        View::throwError($error);

    }
    public function shutdown_functions()
    {
        $error=error_get_last();
        if(!empty($error)){
            View::throwError($error);
        }
    }

}
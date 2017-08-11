<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2017 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://zjzit.cn>
// +----------------------------------------------------------------------

namespace ke\exception;

use ke\Exception;
use ke\Log;
use ke\View;
use ke\Request;

/**
 * ThinkPHP错误异常
 * 主要用于封装 set_error_handler 和 register_shutdown_function 得到的错误
 * 除开从 think\Exception 继承的功能
 * 其他和PHP系统\ErrorException功能基本一样
 */
class ErrorException extends Exception
{
    /**
     * 用于保存错误级别
     * @var integer
     */
    protected $severity;

    /**
     * 错误异常构造函数
     * @param integer $severity 错误级别
     * @param string  $message  错误详细信息
     * @param string  $file     出错文件路径
     * @param integer $line     出错行号
     * @param array   $context  错误上下文，会包含错误触发处作用域内所有变量的数组
     */
    public function __construct($severity, $message, $file, $line, array $context = [])
    {
        $error=[
            'severity'=>$severity,
            'message'=>$message,
            'file'=>$file,
            'line'=>$line,
            'code'=>0
        ];

        $debug=DEBUG;
        $error['message']=isset($error['message']) ? $error['message'] : null;
        $error['file']=isset($error['file']) ? $error['file'] : null;
        $error['line']=isset($error['line']) ? $error['line'] : null;
        $error['severity']=isset($error['severity']) ? $error['severity'] : null;
        $trace=$context;
        Log::write(sprintf(" [ 发生时间 ] %s [ 加载文件数 ] %s \r\n [ error ] %s\r\n [ file ] %s [ line ] %s [ leval ] %s",date('Y-m-d H:i:s'),count(get_included_files()),$error['message'],$error['file'],$error['line'],$severity));
        /*if(Request::isAjax()){
            header('Content-type:application/json');
            if(!$debug){
                $error['message']='系统异常停止';
            }
            View::json(403,$error['message']);
        }*/
        header('status: 505 Not Found');
        require CORE_PATH.'tpl/error.php';
        die();
    }
}

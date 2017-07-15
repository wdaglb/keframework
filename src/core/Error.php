<?php

namespace ke;

use ke\exception\ErrorException;

class Error
{
    /**
     * 注册异常处理
     * @return void
     */
    public function __construct()
    {
        error_reporting(0);
        //ini_set('display_errors',0);
        set_error_handler([$this, 'appError']);
        set_exception_handler([$this, 'appException']);
        register_shutdown_function([$this, 'appShutdown']);
    }

    public function appError($errno, $errstr, $errfile = '', $errline = 0, $errcontext = [])
    {
        throw new ErrorException($errno, $errstr, $errfile, $errline, $errcontext);
    }
    /**
     * Exception Handler
     * @param  \Exception|\Throwable $e
     */
    public function appException($e)
    {
        throw new ErrorException($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine());

    }

    /**
     * Shutdown Handler
     */
    public function appShutdown()
    {
        if (!is_null($error = error_get_last())) {
            throw new ErrorException($error['type'], $error['message'], $error['file'], $error['line']);
        }
        // 写入日志
        //Log::write();
    }

    /**
     * 确定错误类型是否致命
     *
     * @param  int $type
     * @return bool
     */
    protected function isFatal($type)
    {
        return in_array($type, [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE]);
    }
}

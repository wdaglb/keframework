<?php
/**
 * Name: index.php
 * User: King east
 * Site: http://cms.iydou.cn/
 */

// 配置调试模式
define('DEBUG',true);
// 配置工程根目录
define('ROOT',__DIR__.'/../');
// 载入composer自动加载
require ROOT . 'vendor/autoload.php';
// 启动框架
ke\KE::boot();
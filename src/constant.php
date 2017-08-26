<?php
/**
 * 环境常量配置
 */

define('VERSION','1.37');

if(!defined('ROOT')){
    exit('ROOT is empty');
}
defined('CORE_PATH') or define('CORE_PATH', __DIR__.'/');

defined('DEBUG') or define('DEBUG',false);
defined('APP_PATH') or define('APP_PATH',ROOT.'app/');
defined('CONF_PATH') or define('CONF_PATH',ROOT.'config/');
defined('RUNTIME_PATH') or define('RUNTIME_PATH',ROOT.'runtime/');
defined('EXTENDS_PATH') or define('EXTENDS_PATH',ROOT.'extends/');

$dir=dirname($_SERVER['SCRIPT_NAME']);
defined('__WEB_PATH__') or define('__WEB_PATH__',($dir=='\\' || $dir=='/' ? '' : $dir).'/assets');
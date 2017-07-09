<?php
/**
 * Name: start.php
 * User: King east
 * Site: http://cms.iydou.cn/
 */
require ROOT.'vendor/autoload.php';
define('VERSION','1.0.00');
// HOOK类型
define('HOOK_ROUTE_START',1);
define('HOOK_ROUTE_END',2);
define('HOOK_VIEW_START',3);
define('HOOK_VIEW_END',4);

\ke\KE::boot();
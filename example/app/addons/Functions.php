<?php
/**
 * Name: Functions.php
 * User: King east
 * Site: http://cms.iydou.cn/
 */

namespace app\addons;


class Functions
{
    public function url($u,array $a=[])
    {
        return Route::url($u,$a);
    }

}
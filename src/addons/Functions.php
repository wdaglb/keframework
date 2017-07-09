<?php
/**
 * Name: Functions.php
 * User: King east
 * Site: http://cms.iydou.cn/
 */

namespace ke;


class Functions
{
    public function url($uri,$param=[])
    {
        return Route::url($uri,$param);
    }

}
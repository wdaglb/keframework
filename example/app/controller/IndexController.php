<?php
/**
 * Name: IndexController.php
 * User: King east
 * Site: http://cms.iydou.cn/
 */

namespace app\controller;


class IndexController extends Controller
{
    public function index()
    {
        return $this->render('welcome');
    }

}
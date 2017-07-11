<?php
/**
 * Name: Auth.php
 * User: King east
 * Site: http://cms.iydou.cn/
 */

namespace ke;


class Auth
{
    private static $info=[];
    private static $session='';

    private static $error='';
    /**
     * 判断当前是否登陆
     * @return bool
     */
    public static function is_login()
    {
        if(empty(self::$info)) return false;
        if(empty(self::$session)) return false;
        $d=DB::query('SELECT id FROM `:user` WHERE `token`=:token AND `id`=:id',['token'=>self::$session,'id'=>self::$info['id']])->fetch();
        if(!$d) return $false;
        return true;
    }

    public static function error()
    {
        return self::$error;
    }
    private static function setError($content)
    {
        self::$error=$content;
    }

    public static function login($username,$password)
    {
        if($username=='' || is_null($username) || $password=='' || is_null($password)){
            self::setError('登录名与密码为空');
            return false;
        }
        $d=DB::query('SELECT * FROM `:admin` WHERE `username`=?',$username)->fetch();
        if(!$d){
            self::setError('查询不到与该登录名的信息');
            return false;
        }
        if($d['password']!=strtoupper(md5($password))){
            self::setError('登陆密码错误');
            return false;
        }
        $token=sha1($username.$_SERVER['REQUEST_TIME']);
        DB::execute('UPDATE `:admin` SET `token`=? WHERE `id`=?',[$token,$d['id']]);
        self::$info=$d;
        self::$session=$token;
        return true;


    }

}
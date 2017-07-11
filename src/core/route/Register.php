<?php
/**
 * Name: Register.php
 * User: King east
 * Site: http://cms.iydou.cn/
 */

namespace ke\route;


use ke\Config;
use ke\Request;
use ke\View;

class Register
{
    private $group=[];

    /**
     * Register constructor.
     * @param array $option
     */
    public function __construct($option=[])
    {
        $this->group=$option;
    }

    /**
     * 添加路由规则
     * @param $name
     * @param $bind
     * @param string $url
     */
    public function add($name,$bind,$url='')
    {
        $prefix=isset($this->group['prefix']) ? $this->group['prefix'].'/' : '';
        $namespace=isset($this->group['namespace']) ? $this->group['namespace'].'/' : '';
        Lists::set([
            'namespace'=>$namespace,
            'pattern'=>$prefix.$name,
            'bind'=>$namespace.$bind,
            'url'=>$url,
            'domain'=>isset($this->group['domain']) ? $this->group['domain'] : '',
        ]);
    }

    /**
     * 匹配路由
     * @param $url
     */
    public function match($url='')
    {
        $host=get_domain();
        $route=Lists::get();
        foreach ($route as $item){
            if($item['domain']!='' && $item['domain']!=$host){
                continue;
            }
            if($url==$item['pattern']){
                $this->run($item);
                exit;
            }elseif($this->is_rule($url,$item['pattern'])){
                $this->run($item);
                exit;
            }
        }
        if(Request::is_ajax()){
            header('Content-type: application/json');
            exit(json_encode(['status'=>false,'message'=>$message]));
        }
        header('HTTP/1.1 404 Not Found');
        $host=$this->get_server().$url;
        require Request::get('system.framework').'tpl/404.php';
    }
    private function is_rule($url,$route)
    {
        if(strpos($route,'{')===false) return false;
        $key=[];
        $this->get_preg($route);
        $route=preg_replace_callback('/\{(\w+)\}/',function ($to) use(&$key){
            $key[]=$to[1];
            return '(\w+)';
        },$route);
        if(preg_match_all($route,$url,$matchs,PREG_SET_ORDER)){
            foreach ($key as $index=>$name){
                $_GET[$name]=$matchs[$index][1];
                $_REQUEST[$name]=$_GET[$name];
            }
            return true;
        }
        return false;
    }

    /**
     * 替换成正则表达式
     * @param $s
     */
    private function get_preg(&$s)
    {
        $s=str_replace('/','\/',$s);
        $s='/'.$s.'/';
    }

    /**
     * 获取当前完整域名
     * @return string
     */
    private function get_server($domain='')
    {
        if(Request::is_https()){
            $prefix='https://';
        }else{
            $prefix='http://';
        }
        $c=Config::get('is_simple_url');
        if($domain==''){
            $domain=get_domain();
        }
        return $prefix.$domain.($c?'':$_SERVER['SCRIPT_NAME']);
    }

    /**
     * 执行控制器
     * @param $route
     */
    private function run($route)
    {
        $exp=explode('/',$route['bind']);
        $n=count($exp);
        if($n==1){
            list($module,$controller,$action)=[$route['bind'],'index','index'];
        }elseif($n==2){
            $controller=end($exp);
            array_pop($exp);
            $module=end($exp);
            array_pop($exp);
            $action='index';
        }else{
            $action=end($exp);
            array_pop($exp);
            $controller=end($exp);
            array_pop($exp);
            $module=implode('/',$exp);

        }
        $namespaces=sprintf('%s\\%s',str_replace('/','\\',$module),ucwords($controller));
        $namespace='app\\controllers\\'.str_replace('/','\\',$namespaces);
        if(!class_exists($namespace)){
            View::error('控制器不存在:'.$namespace);
        }
        $class=new $namespace();
        if(!method_exists($class,$action)){
            View::error('控制器不存在:'.$namespace.'@'.$action);
        }
        if(Config::get('is_tpl_module')==true){
            \view()->setConfig(['module'=>$module]);
        }
        $return=$class->$action();
        Request::set('module',$module);
        Request::set('controller',$controller);
        Request::set('action',$action);
        if(is_array($return)){
            header('Content-type:application/json');
            echo json_encode($return);
        }else{
            header('Content-Type: text/html; charset=utf-8');
            echo $return;
        }
    }

    /**
     * 生成URL
     * @param $uri
     * @param array $param
     * @return mixed|string
     */
    public function url($uri,$param=[])
    {
        $list=Lists::get();
        if($this->get_index($uri,$list)===false){
            $url='/';
        }else{
            $url=$this->return_rule($list,$param);
        }
        return urldecode($url);
    }

    /**
     * 根据控制器获取URL
     * @param $uri
     * @param $array
     * @return bool
     */
    private function get_index($uri,&$array)
    {
        foreach ($array as $item){
            if($uri==$item['bind']){
                if(isset($item['domain']) && $item['domain']!=''){
                    //
                    if($item['domain']==get_domain()){
                        return false;
                    }
                }
                $array=$item;
                return true;
            }
        }
        return false;
    }

    private function return_rule($item,$param=[])
    {
        $uri=$item['pattern'];
        if(strpos($uri,'{')===false){
            if(!empty($param)){
                $uri.='?'.http_build_query($param);
            }
            return $this->get_server($item['domain']).$uri;
        }

        if(preg_match_all('/\{(\w+)\}/',$uri,$matchs,PREG_SET_ORDER)){
            foreach ($matchs as $item){
                if(isset($param[$item[1]])){
                    $uri=str_replace($item[0],$param[$item[1]],$uri);
                }else{
                    $uri=str_replace($item[0],'0',$uri);
                }
            }
            if(!empty($param)){
                $uri.='?'.http_build_query($param);
            }
        }
        return $this->get_server($item['domain']).$uri;


    }

}
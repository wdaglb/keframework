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

    private $index=-1;

    /**
     * Register constructor.
     * @param array $option
     */
    public function __construct($option=[])
    {
        $this->group=$option;
    }

    public function add($name,$controller,$action='',$type='get')
    {
        $prefix=isset($this->group['prefix']) ? $this->group['prefix'].'/' : '';
        $module=isset($this->group['module']) ? $this->group['module'].'/' : '';
        $this->index=Lists::set([
            'pattern'=>$prefix.$name,
            'module'=>$module,
            'controller'=>$controller,
            'action'=>$action,
            'type'=>$type,
            'domain'=>isset($this->group['domain']) ? $this->group['domain'] : '',
        ]);
        return $this;
    }

    public function __call($name, $arguments)
    {
        if(in_array($name,['as'])){
            $method='_'.$name;
            return call_user_func_array([$this,$method],$arguments);
        }
    }

    /**
     * 绑定生成URL名
     * @param $url
     */
    public function _as($url)
    {
        Lists::push($this->index,['as'=>$url]);
    }

    /**
     * 匹配路由
     * @param $url
     */
    public function match($url='')
    {
        if(strpos($url,'?')){
            list($url,$param)=explode('?',$url);
            parse_str($param,$_GET);
        }
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
        $host=$this->get_server().$url;
        header('HTTP/1.1 404 Not Found');
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
        /*
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
        $namespace='app\\controllers\\'.str_replace('/','\\',$namespaces);*/
        if(!class_exists($route['controller'])){
            View::error('控制器不存在:'.$route['controller']);
        }
        $class=new $route['controller']();
        if(!method_exists($class,$route['action'])){
            View::error('控制器不存在:'.$route['controller'].'@'.$route['action']);
        }
        $module=isset($route['module']) ? $route['module'] : '';
        Request::set('module',$module);
        Request::set('controller',$route['controller']);
        Request::set('action',$route['action']);

        if(Config::get('is_tpl_module')==true){
            \view()->setConfig(['module'=>$module]);
        }
        if(isset($class->fronts)){
            foreach ($class->fronts as $method) $class->$method();
        }


        $return=$class->$route['action']();
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
        if(strpos($uri,'@')===false){
            // 使用约定名
            foreach ($array as $item){
                // 寻找约定名
                if(isset($item['as']) && $uri==$item['as']){
                    if(isset($item['domain']) && $item['domain']!=''){
                        //
                        if($item['domain']==get_domain()){
                            $array=$item;
                            return true;
                        }
                    }
                    $array=$item;
                    return true;
                }
            }
        }else{
            // 使用控制器
            $uris='app\\controllers\\'.str_replace('/','\\',$uri);
            list($controller,$action)=explode('@',$uris);
            foreach ($array as $item){
                // 寻找约定名
                if(isset($item['controller']) && $controller==$item['controller'] && $item['action']==$action){
                    if(isset($item['domain']) && $item['domain']!=''){
                        //
                        if($item['domain']==get_domain()){
                            $array=$item;
                            return true;
                        }
                    }
                    $array=$item;
                    return true;
                }
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
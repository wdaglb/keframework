<?php
/**
 * Name: Register.php
 * User: King east
 * Site: http://cms.iydou.cn/
 */

namespace ke\route;


use ke\Config;
use ke\Exception;
use ke\Log;
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

    public function add($name,$bind,$method='GET',$domain='')
    {
        $prefix=isset($this->group['prefix']) ? $this->group['prefix'].'/' : '';
        $namespace=isset($this->group['namespace']) ? $this->group['namespace'].'/' : '';
        if(is_array($bind)){
            $as=$bind['as'];
            $bind=$bind['uses'];
        }else{
            $as='';
        }
        $this->index=Lists::set([
            'name'=>$as,
            'pattern'=>$prefix.$name,
            'bind'=>$namespace.$bind,
            'method'=>$method,
            'domain'=>($domain=='' ? (isset($this->group['domain']) ? $this->group['domain'] : '') : $domain),
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
            if($item['method']!='any' && $item['method']!=$this->getMethod()){
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
        $host=$this->get_server().$url;
        
        header('status: 404 Not Found');
        view()->miss($host);
    }
    private function is_rule($url,$route)
    {
        if(strpos($route,'{')===false) return false;
        $key=[];
        $this->get_preg($route);
        $route=preg_replace_callback('/\{(\w+)(\?*)\}/',function ($to) use(&$key){
            $key[]=$to[1];
            if($to[2]==''){
                return '(\w+)';
            }else{
                return '*(\w*)';
            }
        },$route);
        if(preg_match_all($route,$url,$matchs,PREG_SET_ORDER)){
            foreach ($key as $index=>$name){
                $_GET[$name]=$matchs[0][$index+1];
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
        $s='/'.$s.'$/';
    }

    /**
     * 获取当前完整域名
     * @return string
     */
    private function get_server($domain='')
    {
        if(Request::isHttps()){
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
        if(!preg_match('/(?P<module>.*?)\/?(?P<controller>\w+)Controller@(?P<action>\w+)/',$route['bind'],$match)){
            throw new Exception('绑定控制器规则定义错误:'.$route['bind']);
        }
        $namespaces=sprintf('%scontroller\\%s',($match['module']=='' ? '' : str_replace('/','\\',$match['module']).'\\'),$match['controller'].'Controller');
        $namespace='app\\'.str_replace('/','\\',$namespaces);
        if(!class_exists($namespace)){
            throw new Exception('控制器不存在:'.$namespace);
        }
        $class=new $namespace();
        if(!method_exists($class,$match['action'])){
            throw new Exception('控制器不存在:'.$namespace.'@'.$match['action']);
        }
        Request::set('module',$match['module']);
        Request::set('controller',strtolower($match['controller']));
        Request::set('action',pascal_to_line($match['action']));

        if(Config::get('is_tpl_module')==true){
            \view()->setConfig(['module'=>$match['module']]);
        }
        if(Config::get('is_tpl_controller')==true){
            \view()->setConfig(['controller'=>strtolower($match['controller'])]);
        }
        if(method_exists($class,'getAttr')){
            if($class->getAttr('fronts')){
                foreach ($class->getAttr('fronts') as $method) $class->$method();
            }
        }
        if(method_exists($class,'_init')){
            $class->_init();
        }


        \view()->assign('request',Request::get());
        $return=$class->{$match['action']}();
        if(is_array($return)){
            header('Content-type:application/json');
            echo json_encode($return);
        }else{
            header('Content-Type: text/html; charset=utf-8');
            echo $return;
        }
    }

    private function getMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
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
            $url=$uri;
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
                if(isset($item['name']) && $uri==$item['name']){
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
            foreach ($array as $item){
                // 寻找约定名
                if(isset($item['bind']) && $uri==$item['bind']){
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
                $uri.='?';
                if(is_array($param)){
                    $uri.=http_build_query($param);
                }else{
                    $uri.=$param;
                }
            }
            return $this->get_server($item['domain']).$uri;
        }

        if(preg_match_all('/\{(\w+)\?*\}/',$uri,$matchs,PREG_SET_ORDER)){
            foreach ($matchs as $tm){
                if(isset($param[$tm[1]])){
                    $uri=str_replace($tm[0],$param[$tm[1]],$uri);
                    unset($param[$tm[1]]);
                }else{
                    $uri=str_replace($tm[0],'0',$uri);
                }
            }
            if(!empty($param)){
                $uri.='?';
                if(is_array($param)){
                    $uri.=http_build_query($param);
                }else{
                    $uri.=$param;
                }
            }
        }
        return $this->get_server($item['domain']).$uri;


    }

}
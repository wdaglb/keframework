<?php
namespace ke;


class Validate
{
    private $rule=[];
    // 自定义提示消息
    private $msge=[];
    // 自带提示消息
    private $msg=[
        'require'=>'[replace]不能为空',
        'max'=>'[replace]长度不允许超过:[max]',
        'min'=>'[replace]长度最小需要:[min]',
        'int'=>'[replace]只能为数字',
        'eng'=>'[replace]只能为字母',
        'engint'=>'[replace]只能为字母与数字',
        'email'=>'[replace]格式不符',
        'ip'=>'[replace]格式不符',
        'url'=>'[replace]格式不符',
        'between'=>'[replace]只能在[min]-[max]间',
        'noteq'=>'[replace]不能为[value]'
    ];
    private $from;
    private $error=null;
    public function __construct($rule=[],$msg=[]){
        foreach ($rule as $key=>$value){
            $value=strtolower($value);
            if(strpos($value, '|')===false){
                $this->rule[$key]=[$value];
            }else{
                $tmp=explode('|',$value);
                $this->rule[$key]=$tmp;
            }
        }
        foreach ($msg as $key=>$value){
            $keys=strtolower($key);
            if(strpos($keys,'.')===false){
                $this->msge[$keys]=$value;
            }else{
                list($left,$right)=explode('.',$keys);
                $this->msge[$left][$right]=$value;
            }
        }
    }
    public function getError(){
        return $this->error;
    }
    public function check($post){
        $r=true;
        $this->from=$post;
        foreach ($this->rule as $key=>$value){
            $r=$this->checks($key, $value);
            if(!$r){
                break;
            }
        }
        return $r;
    }
    private function checks($name,$params){
        foreach ($params as $value){
            if(strpos($value,':')===false){
                $method='_'.$value;
            }else{
                $tmp=explode(':',$value);
                $method='_'.$tmp[0];
            }
            if(method_exists($this,$method)){
                if(!$this->$method($value,$name)){
                    return false;
                }
            }
        }
        return true;
    }
    private function _require($name,$from){
        if(!isset($this->from[$from]) || $this->from[$from]=='' || is_null($this->from[$from])){
            $this->setMsg($name,$from);
            return false;
        }
        return true;
    }
    private function _between($name,$from){
        if(empty($this->from[$from])){
            return true;
        }
        list($name,$tmp)=explode(':',$name);
        list($min,$max)=explode(',',$tmp);
        if(mb_strlen($this->from[$from],'utf-8')>=$min && mb_strlen($this->from[$from],'utf-8')<=$max){
            return true;
        }else{
            $this->setMsg($name,$from);
            $this->error=str_replace('[min]',$min,$this->error);
            $this->error=str_replace('[max]',$max,$this->error);
            return false;
        }
    }
    private function _max($name,$from){
        if(empty($this->from[$from])){
            return true;}
        $tmp=explode(':',$name);
        $n=mb_strlen($this->from[$from],'utf-8');
        if($n>(int)$tmp[1]){
            $this->setMsg($tmp[0],$from);
            $this->error=str_replace('[max]',$tmp[1],$this->error);
            return false;
        }else{
            return true;
        }
    }
    private function _min($name,$from){
        if(empty($this->from[$from])){
            $this->setMsg($name,$from);
            return true;
        }
        $tmp=explode(':',$name);
        $n=mb_strlen($this->from[$from],'utf-8');
        if($n<(int)$tmp[1]){
            $this->setMsg($tmp[0],$from);
            $this->error=str_replace('[min]',$tmp[1],$this->error);
            return false;
        }else{
            return true;
        }
    }
    private function _int($name,$from){
        if(!isset($this->from[$from])){
            $this->setMsg($name,$from);
            return false;
        }
        if(preg_match('/^\d*$/',$this->from[$from])){
            return true;
        }else{
            $this->setMsg($name,$from);
            return false;
        }
    }
    private function _eng($name,$from){
        if(!isset($this->from[$from])){
            return true;
        }
        if(preg_match('/^[A-Za-z]+$/',$this->from[$from])){
            return true;
        }else{
            $this->setMsg($name, $from);
            return false;
        }
    }
    private function _engint($name,$from){
        if(!isset($this->from[$from])){
            return true;
        }
        if(preg_match('/^[A-Za-z0-9]+$/',$this->from[$from])){
            return true;
        }else{
            $this->setMsg($name, $from);
            return false;
        }
    }
    private function _email($name,$from){
        if(!isset($this->from[$from])){
            return true;
        }
        if(filter_var($this->from[$from], FILTER_VALIDATE_EMAIL)){
            $mx=explode("@",$this->from[$from]);
            $mx=array_pop($mx);
            if(checkdnsrr($mx,"MX")){
                return true;
            }else{
                $this->setMsg($name, $from);
                return false;
            }
        }else{
            $this->setMsg($name, $from);
            return false;
        }
    }
    private function _ip($name,$from){
        if(!isset($this->from[$from])){
            return true;
        }
        if(filter_var($this->from[$from], FILTER_VALIDATE_IP,FILTER_FLAG_NO_PRIV_RANGE|FILTER_FLAG_NO_RES_RANGE)){
            return true;
        }else{
            $this->setMsg($name, $from);
            return false;
        }
    }
    private function _url($name,$from){
        if(!isset($this->from[$from])){
            return true;
        }
        if(filter_var($this->from[$from], FILTER_VALIDATE_URL)){
            return true;
        }else{
            $this->setMsg($name, $from);
            return false;
        }
    }
    /*
     * 不存在
     */
    private function _noteq($name,$from){
        if(!isset($this->from[$from])){
            $this->setMsg('noteq',$from);
            return false;
        }
        list($n,$val)=explode(':',$name);
        if(!strstr($val,',')===false){
            $tmp=explode(',',$val);
            if(in_array($this->from[$from],$tmp)){
                $this->setMsg($n,$from);
                return false;
            }else{
                return true;
            }
        }else{
            if($val==$this->from[$from]){
                $this->setMsg($n,$from);
                return false;
            }else{
                return true;
            }
        }
    }
    private function getMessage($name)
    {

    }
    private function setMsg($name,$from)
    {
        if(!strpos($name,':')===false){
            $tmp=explode(':',$name);
            $name=$tmp[0];
        }
        if(isset($this->msge[$from][$name])){
            $error=$this->msge[$from][$name];
        }else{
            if(isset($this->msge[$from]) && !is_array($this->msge[$from])){
                $error=$this->msge[$from];
            }else{
                $error=$this->msg[$name];
            }
        }
        $error=str_replace('[replace]',$from,$error);
        $this->error=$error;
    }
    private function getReg($name){
        $reg=null;
        if(!(strpos($name,':')===false)){
            $tmp=explode(':',$name);
            $name=$tmp[0];
        }
        switch ($name){
            case 'max':
                break;
            default:
                break;
        }
        return '/'.$reg.'/';
    }

}
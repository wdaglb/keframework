<?php
namespace ke;


class Verification
{
    private $id=0;
    private $src=[];
    private $width=64;
    private $height=64;
    private $error='';
    public function __construct($id='',$len=4,$height=25){
        if($id!=''){
            mt_srand();
            for ($i = 0; $i < $len; $i++) {
                $this->src[]=mt_rand(0,9);
            }
            $this->id=crc32($id);
            $this->width=$len*10+15;
            $this->height=$height;
        }
    }
    public function check($id,$value){
        $id=crc32($id);
        $ver=session('Verification_'.$id);
        session('Verification_'.$id,null);
        return $ver==$value;
    }
    public function error(){
        return $this->error;
    }
    public function show(){
        header("Content-Type:image/png");
        $im = imagecreate($this->width,$this->height);
        //背景色
        $back = imagecolorallocate($im, 0xFF, 0xFF, 0xFF);
        //模糊点颜色
        $pix  = imagecolorallocate($im, 187, 230, 155);
        //字体色
        $font = imagecolorallocate($im,50,100,200);
        //绘模糊作用的点
        mt_srand();
        for ($i = 0; $i < 1000; $i++) {
            imagesetpixel($im, mt_rand(0, $this->width), mt_rand(0, $this->height), $pix);
        }
        //输出字符
        $len=count($this->src);
        $fonts=CORE_PATH.'extends/font/cour.ttf';
        $check='';
        for ($i=0;$i<$len;$i++){
            $check.=$this->src[$i];
            imagettftext($im, 14, mt_rand(-25,25), 5+$i*10, 18, $font, $fonts, $this->src[$i]);
        }
        session('Verification_'.$this->id,$check);
        //输出矩形
        imagerectangle($im, 0, 0, $this->width -1, $this->height -1, $back);
        //输出图片
        imagepng($im);
        imagedestroy($im);
        $im=null;
    }



}
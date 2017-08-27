<?php


namespace ke;


class Image
{
    private $info;
    private $type;
    private $content;
    private $color;
    private $im;
    private $font;

    public function text($src,$content,$savepath)
    {
        //获取图片信息
        $info = getimagesize($src);
        //获取图片扩展名
        $type = image_type_to_extension($info[2],false);
        // 创建新图片
        $fun = "imagecreatefrom{$type}";
        $old = $fun($src);
        //动态的把图片导入内存中
        $image=imagecreate($info[0],$info[1]+10);

        imagecopy($image,$old,0,0, 0, 0,$info[0],$info[1]);
        //指定字体颜色
        $col = imagecolorallocate ($image,0,0,0);
        //指定字体内容
        $fonts=CORE_PATH.'extends/font/fz.ttf';
        $rect=imagettfbbox(18,0,$fonts,$content);
        $w=$rect[2]-$rect[0];

        //给图片添加文字
        imagettftext($image,18,0,$info[0]/2-$w/2,$info[1],$col,$fonts,$content);
        //指定输入类型
        header('Content-type:'.$info['mime']);
        //动态的输出图片到浏览器中
        $func = "image{$type}";
        $func($image);
        switch ($type){
            case 'jpeg':
                imagejpeg($image,$savepath);
                break;
            case 'gif':
                imagegif($image,$savepath);
            default:
                imagepng($image,$savepath);
                break;
        }
        //销毁图片
        imagedestroy($image);

    }

}
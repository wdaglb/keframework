<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="UTF-8">
    <title>System Error</title>
</head>
<body>
    <style type="text/css">
        .exception-hr{
            border: none;
            border-top: solid 1px #dddddd;
        }
        .exception-h1{
            color: #00FF66;
        }
        .exception-h3{
            color: #8D8D8D;
            font-weight: 100;
            font-size: 1.0em;
        }

        .exception-main{
            background: #2B2E37;
            margin: 0 auto;
            max-width: 1024px;
            padding: 10px;
        }
        .exception-code{
            background: #333333;
            outline:none;
            height: 400px;
            overflow: auto;
            font-size: 12px;
            padding: 5px;
            color: #fff9ec;
        }
        .exception-code p{
            margin: 5px 5px 5px 15px;
        }
    </style>
    <div class="exception-main">
        <h1 class="exception-h1">System Error</h1>
        <?php if($debug):?>
        <h3 class="exception-h3"><?php echo $error['message'];?></h3>
            <hr/>
        <div class="exception-code" contenteditable="true">
            <p><b>#0 </b>
                <?php echo $error['file'];?>(<?php echo $error['line'];?>)
            </p>
            <?php foreach ($trace as $key=>$item):?>
                <p><b>#<?php echo $key+1;?> </b>
                <?php if(isset($item['file'])):?>
                    <?php echo $item['file'];?>(<?php echo $item['line'];?>)&nbsp;<?php endif;?>
                    <span><?php echo (isset($item['class'])?$item['class']:'').(isset($item['type'])?$item['type']:'').$item['function'];?>(<?php $e=end($item['args']);?><?php foreach ($item['args'] as $tmp):?><?php echo is_string($tmp) ? '\''.$tmp.'\'' : 'Array';?><?php if($e!=$tmp):?>,<?php endif;?><?php endforeach;?>)</span>
                </p>
            <?php endforeach;?>
        </div>
        <?php endif;?>
        <hr class="exception-hr">
        <h3 class="exception-h3">
            KE-Framework V<?php echo VERSION;?> {&nbsp;<?php echo date('Y-m-d H:i:s');?>&nbsp;}
        </h3>
    </div>
</body>
</html>
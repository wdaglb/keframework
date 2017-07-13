<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="UTF-8">
    <title>System Error</title>
    <style type="text/css">
        table{
            color: #000;
            border-collapse: collapse;
        }
        table td,table th{
            border: solid 1px #dddddd;
            padding: 8px;
        }
        body{
            padding: 10px;
        }
        hr{
            border: none;
            border-top: solid 1px #dddddd;
        }
        h3{
            color: #8D8D8D;
            font-weight: 100;
            font-size: 1.0em;
        }
    </style>
</head>
<body>
<h1>System Error</h1>
<?php if($debug):?>
<table>
    <tr>
        <th width="80">错误类型</th>
        <th>错误描述</th>
    </tr>
    <?php if(isset($error)):?>
    <?php foreach ($error as $key=>$item):?>
    <tr>
        <td><?php echo $key;?></td>
        <td><?php echo $item;?></td>
    </tr>
    <?php endforeach;?>
    <?php endif;?>
</table>
<?php endif;?>
<hr>
<h3>
    KE-FRAMEWORK V<?php echo VERSION;?> {&nbsp;<?php echo date('Y-m-d H:i:s');?>&nbsp;}
</h3>
</body>
</html>
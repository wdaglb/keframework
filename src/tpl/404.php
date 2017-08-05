<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="UTF-8">
    <title>System Error</title>
    <style type="text/css">
        .p404-body{
            padding: 50px;
            max-width: 1024px;
            margin: 0 auto;
        }
        .p404-hr{
            border: none;
            border-top: solid 1px #dddddd;
        }
        .p404-h1{
            display: inline-block;
            margin: 0;
        }
        .p404-h3{
            margin: 0;
            display: inline-block;
            color: #8D8D8D;
            font-weight: 100;
            font-size: 1.0em;
        }
    </style>
</head>
<body class="p404-body">
<div>
<h1 class="p404-h1">404</h1>
<h3 class="p404-h3">你正在访问的页面不存在或已被删除</h3>
</div>
<small><b>URL</b>&nbsp;<span style="color: #00ff00"><?php echo $host;?></span></small>
<hr class="p404-hr">
<h3 class="p404-h3">
    KE-Framework 404状态页面 {&nbsp;<?php echo date('Y-m-d H:i:s');?>&nbsp;}
</h3>
</body>
</html>
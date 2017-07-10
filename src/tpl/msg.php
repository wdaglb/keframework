<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="UTF-8">
    <title>System Error</title>
    <style type="text/css">
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
<h1><?php echo $message;?></h1>
<hr>
<h3>
    KE-FRAMEWORK V<?php echo VERSION;?> {&nbsp;<?php echo date('Y-m-d H:i:s');?>&nbsp;}
</h3>
</body>
</html>
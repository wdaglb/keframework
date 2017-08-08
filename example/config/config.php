<?php
/**
 * Name: config.php
 * User: King east
 * Site: http://cms.iydou.cn/
 */

return [
    // 是否开启底部调试信息
    'ke-debug'=>false,
    // 是否开启域名根
    'url_domain_root'=>false,

    // 是否开启简洁路由
    'is_simple_url'=>true,

    // 是否自动分配模板目录
    'is_tpl_module'=>true,
    // 是否自动分配控制器目录
    'is_tpl_controller'=>true,

    // 模板引擎
    'template'=>[
        // 默认支持twig
        'type'=>'twig',
        // 模板后缀
        'suffix'=>'.htm'
    ],

    // 数据库配置
    'database'=>[
        'host'=>'127.0.0.1',
        'name'=>'test',
        'user'=>'root',
        'pass'=>'root',
        'charset'=>'utf-8',
        'prefix'=>'ke_',
        'port'=>3306
    ],
];
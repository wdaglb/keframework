<?php
namespace interfaces;


interface Cache
{
    /**
     * 初始化
     * Template constructor.
     * @param array $name
     */
    public function __construct(array $name);

    /**
     * 取得缓存值
     */
    public function get($key,$value='');

    /**
     * 设置缓存
     */
    public function set($key,$value='');
}
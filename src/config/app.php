<?php

return [

    'web_name' => 'Alice Blog',

    'seo_title' => 'Alice的博客', #SEO默认标题

    'seo_keywords' => 'PHP技术博客', #SEO默认关键字

    'seo_description' => 'Symfony2,Composer,自己设计博客框架。', #SEO默认描述

    'debug' => true, #是否打印詳細錯誤

    'charset' => 'UTF-8',

    'logger' => null,

    'session_pre' => 'Alice', #Session前缀

    'cookie_pre' => 'Alice', #Cookie前缀

    'baidu_site_api' => 'http://data.zz.baidu.com/urls?site=www.lzxya.com&token=FmPt3beiE9VEhM9b',

    # 数据库配置~
    'db_options' => [

        'driver'    => 'mysql',

        'host'      => 'localhost',

        'database'  => 'newalice',

        'username'  => 'root',

        'password'  => 'root',

        'charset'   => 'utf8',

        'collation' => 'utf8_unicode_ci',

        'prefix'    => '',
    ],

    # 缓存相关配置~
    'memcache_switch'  => FALSE, #是否开启Memcache缓存，TRUE是开启

    'httpcache_switch' => FALSE, #是否开启Http缓存(反向代理缓存)，TRUE是开启

    'httpcache_time'   => 60,    #设置Http缓存的时间(单位是秒)

    # Memcache配置~
    'memcache_options' => [

        'host'      => 'localhost',

        'port'      => 11211,

        'expiretime'=> 6*3600, #默認緩存時間

        'prefix'    => 'Alice', #默認緩存Key前綴
    ],

    'mail_switch'   => FALSE, #是否开启邮件回复功能

    'mail_address'  => '13826914162@163.com', #地址

    'mail_password' => 'lzx013632350976', #密码

    'mail_smtp'     => 'smtp.163.com',

    'wx_appid'      => 'wxb33bef172a0860cf', #微信appid

    'wx_appsecret'  => '0b35415865993d1be16b7655a8f673a0', #微信密匙


];
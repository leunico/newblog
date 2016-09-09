<?php

return [

    'name' => 'Alice Blog',

    'seo_title' => 'yi', #SEO默认标题

    'seo_keywords' => 'qi', #SEO默认关键字

    'seo_description' => 'hapi', #SEO默认描述

    'debug' => true, #是否打印詳細錯誤

    'charset' => 'UTF-8',

    'logger' => null,

    'providers' => [

        'Test' => 'Test',

    ],

    # 数据库配置~
    'db_options' => [

        'driver'    => 'mysql',

        'host'      => 'localhost',

        'database'  => 'alice',

        'username'  => 'root',

        'password'  => 'root',

        'charset'   => 'utf8',

        'collation' => 'utf8_unicode_ci',

        'prefix'    => '',
    ],

    # 缓存相关配置~
    'memcache_switch'  => TRUE, #是否开启Memcache缓存，TRUE是开启

    'httpcache_switch' => FALSE, #是否开启Http缓存(反向代理缓存)，TRUE是开启

    # Memcache配置~
    'memcache_options' => [

        'host'      => 'localhost',

        'port'      => 11211,

        'expiretime'=> 6*3600, #默認緩存時間

        'prefix'    => 'Alice', #默認緩存Key前綴
    ],

    #>>>>>>>>>>>>>>>>>其它配置（就不分文件了，因为其实严格意义上放到数据库里面好一点）<<<<<<<<<<<<<<<<<<#    
    'article_class' => [
        
        'php' => 'PHP',
        
        'js' => '前端',
        
        'backend' => '后端技术',
        
        'laravel' => 'Laravel5',
        
        'left' => '生活说',
        
        'linux' => 'Linux',
        
        'music' => '音乐',
        
        'game' => '游戏',
        
        'book' => '图书',
        
        'python' => 'Python',
            
     ],
    
    #设置前端的导航条
    'menu_class' => [
        
        'jishu' => ['jishu' => '技术日记','js' => '前端','linux' => 'Linux','backend' => '后端技术','laravel' => 'Laravel5', 'python' => 'Python'],
        
        'php' => 'PHP',
        
        'symfony' => 'Symfony',
        
        'left' => '生活说',
        
        'like' => ['like' => '爱好分享','music' => '音乐','game' => '游戏','book' => '图书'],
        
        'meclass' => ['meclass' => '关于','me' => '关于我','articlebox' => '归档','liuy' => '留言'],
        
        'timewait' => '时光旅行',
        
    ],
    
    #网站的友链
    'friendslink' => [
        
        '站长之家' => 'http://www.chinaz.com',
        
        'AcFun弹幕网' => 'http://www.acfun.tv',
        
        'BiliBili弹幕网' => 'http://www.bilibili.com',
        
        '开源中国' => 'http://www.oschina.net/',
        
        '08CMS网站' => 'http://www.08cms.com/',
        
        '和平鸽的博客' => 'http://txjia.com/tip/',
        
        'Mireas的博客' => 'http://blog.mireas.com/',
        
        'iAnna的博客' => 'http://blog.ykqmain.com/',
                
        '大猩猩网' => 'http://www.dxxing.com/',
    ],

];
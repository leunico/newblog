<?php

require_once __DIR__.'/../vendor/autoload.php';

#引入配置文件
$config = include __DIR__.'/config/config.php';

#创建框架容器
$app = new Alicecore\AppFramework($config);

#加载路由
Alicecore\Route::start($app);

#加载监听服务(非核心)
#Listen::start($app);

return $app;
<?php

return [

    # 注册Service系统服务
    'view' => 'app\service\ViewService',

    'vice_controller' => 'app\service\ControllerService',

    'weixin' => 'app\service\WeixinService',

    # 注册Middleware中间件
	'menu' => 'app\service\MenuMiddleware',

];
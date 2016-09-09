<?php

use Symfony\Component\HttpFoundation\Request;

$kernel = include __DIR__.'/../src/Alicecore/app.php';

$request = Request::createFromGlobals(); //接受请求

$response = $kernel->handle($request);  //处理请求

$response->send();  //返回响应

$kernel->terminate($request, $response);  //完成一次请求后的处理
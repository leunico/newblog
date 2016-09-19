<?php

use Alicecore\Route;

Route::get('/', 'IndexController::index'); // 名称：GET_
Route::get('/menu/{name}', ['_controller'=>'MenuController::index', '_before_middlewares'=>'menu::boot']);
Route::get('/articleshow/{id}', 'ArticleshowController::index');

#测试用
// 中间件:MiddlewareListener.php 名称：GET_haha_name
Route::get('/haha/{name}', ['_controller'=>'DefaultController::news']);
Route::get('/test', 'DefaultController::index');
Route::get('/mem', 'DefaultController::memcache');

